<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage']);

        if ($request->filled('search') && strlen($request->search) >= 2) {
            $query->where('name', 'LIKE', '%' . trim($request->search) . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()
                          ->paginate(10)
                          ->withQueryString();

        // Dikirim untuk daftar kategori + modal Tambah/Edit Kategori di halaman ini
        // (fitur kelola kategori dipindahkan dari form Tambah Produk ke sini).
        $categories = Category::latest()->get();

        return view('admin.product-index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('admin.product-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'sku_root'     => 'required|unique:products',
            // Satu dropdown "Tipe Penjualan / Satuan" mengendalikan DUA hal sekaligus:
            // (1) kolom lama `satuan` (teks tampilan, dipakai halaman lain) dan
            // (2) kolom baru `unit_type` (saklar logika Pcs vs Kg) — lihat migration
            // add_unit_type_to_products_table untuk alasannya.
            'satuan'       => 'required|in:kg,pcs',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',

            // Validasi kondisional — CABANG "PCS": harga UTAMA wajib diisi hanya jika satuan=pcs.
            // Input-input ini di-nonaktifkan (disabled) oleh Alpine di Blade saat
            // satuan=kg, sehingga browser TIDAK mengirim nilainya sama sekali —
            // required_if karenanya tidak pernah tersandung kondisi kg.
            // Field stok dihapus atas permintaan.
            'price_per_kg' => 'required_if:satuan,pcs|nullable|numeric|min:0',
            'min_pembelian'=> 'required_if:satuan,pcs|nullable|numeric|min:1',

            // Validasi kondisional — CABANG "KG": wajib minimal 1 baris Varian Potongan/Gramasi.
            // Field stok dihapus atas permintaan.
            'variants'            => 'required_if:satuan,kg|nullable|array|min:1',
            'variants.*.label'    => 'required_with:variants|string|max:100',
            'variants.*.price'    => 'required_with:variants|numeric|min:0',

            // Multi-foto: maksimal 5 gambar per produk.
            'images'       => 'nullable|array|max:5',
            'images.*'     => 'image|max:2048',
        ], [
            'images.max'   => 'Maksimal 5 foto per produk.',
            'variants.required_if' => 'Produk bertipe Kg wajib memiliki minimal 1 varian potongan/gramasi.',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['unit_type'] = $data['satuan']; // sinkronkan kolom baru dgn dropdown yang sama

        // LOGIKA IF/ELSE PCS vs KG ------------------------------------------------
        if ($data['satuan'] === 'kg') {
            // Produk Kg TIDAK memakai harga utama untuk transaksi — tapi kita
            // tetap isi otomatis dari data varian (harga termurah) supaya kartu
            // katalog lama & fallback "add-to-cart tanpa varian" tetap
            // menampilkan angka yang masuk akal. Field stok dihapus atas permintaan.
            $variantsInput = collect($request->input('variants', []));
            $data['price_per_kg']  = (float) $variantsInput->min('price');
            $data['min_pembelian'] = 1;
        }
        // Jika 'pcs': price_per_kg/min_pembelian sudah tervalidasi & terisi
        // langsung dari input admin di atas — tidak ada varian yang dibuat.

        $product = Product::create($data);

        // Simpan baris Varian Potongan/Gramasi hanya untuk produk Kg.
        if ($data['satuan'] === 'kg') {
            foreach ($request->input('variants', []) as $v) {
                $product->variants()->create([
                    'label' => $v['label'],
                    'price' => $v['price'],
                ]);
            }
        }

        // Upload multi-foto (maks 5, sudah divalidasi di atas). Foto pertama
        // otomatis jadi foto utama (primaryImage) yang tampil di katalog.
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                $path = $img->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => $i == 0
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan dengan stok awal');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants']);
        return view('admin.product-edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'sku_root'     => 'required|unique:products,sku_root,'.$product->id,
            'satuan'       => 'required|in:kg,pcs',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',

            // Validasi kondisional — sama seperti store(): hanya wajib diisi bila satuan=pcs (lihat komentar di store()).
            // Field stok dihapus atas permintaan.
            'price_per_kg' => 'required_if:satuan,pcs|nullable|numeric|min:0',
            'min_pembelian'=> 'required_if:satuan,pcs|nullable|numeric|min:1',

            // Validasi kondisional — Varian wajib minimal 1 baris bila satuan=kg.
            // Field stok dihapus atas permintaan.
            'variants'            => 'required_if:satuan,kg|nullable|array|min:1',
            'variants.*.label'    => 'required_with:variants|string|max:100',
            'variants.*.price'    => 'required_with:variants|numeric|min:0',

            'images'           => 'nullable|array',
            'images.*'         => 'image|max:2048',
            'delete_images'    => 'array',
            'primary_image_id' => 'nullable|exists:product_images,id',
        ], [
            'variants.required_if' => 'Produk bertipe Kg wajib memiliki minimal 1 varian potongan/gramasi.',
        ]);

        // Guard MAKS 5 FOTO: dihitung dari (foto lama yang TIDAK dihapus) + (foto baru
        // yang diupload) — bukan sekadar count(images.*) seperti di store(), karena di
        // sini ada foto lama yang mungkin tetap dipertahankan.
        $existingCount   = $product->images()->count();
        $deleteCount     = count($request->input('delete_images', []));
        $newUploadCount  = $request->hasFile('images') ? count($request->file('images')) : 0;
        $totalAfterSave  = ($existingCount - $deleteCount) + $newUploadCount;
        if ($totalAfterSave > 5) {
            return back()->withInput()->withErrors([
                'images' => "Maksimal 5 foto per produk. Total foto setelah disimpan akan menjadi {$totalAfterSave}.",
            ]);
        }

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['unit_type'] = $data['satuan']; // sinkron dengan kolom baru (lihat store())

        // LOGIKA IF/ELSE PCS vs KG (sama seperti store()) --------------------------
        // Field stok dihapus atas permintaan.
        if ($data['satuan'] === 'kg') {
            $variantsInput = collect($request->input('variants', []));
            $data['price_per_kg']  = (float) $variantsInput->min('price');
            $data['min_pembelian'] = 1;
        }

        $product->update($data);

        // Ganti seluruh set Varian dengan yang baru disubmit (replace-all): paling
        // sederhana & aman untuk form repeater dinamis (baris bisa ditambah/dihapus
        // bebas oleh Admin). Varian lama yang dipakai di order_items TIDAK merusak
        // riwayat pesanan — kolom variant_id di sana nullOnDelete, dan
        // variant_label_snapshot tetap menyimpan teksnya (lihat migration terkait).
        $product->variants()->delete();
        if ($data['satuan'] === 'kg') {
            foreach ($request->input('variants', []) as $v) {
                $product->variants()->create([
                    'label' => $v['label'],
                    'price' => $v['price'],
                ]);
            }
        }

        // Hapus foto yang dicentang Admin untuk dihapus.
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $id) {
                $img = ProductImage::find($id);
                if ($img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }
        }

        // Set ulang foto utama sesuai pilihan radio Admin (dari galeri yang tersisa).
        if ($request->filled('primary_image_id')) {
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            ProductImage::where('id', $request->primary_image_id)->update(['is_primary' => true]);
        }

        // Tambah foto BARU ke galeri (BUKAN lagi auto-replace foto utama seperti
        // versi lama) — ini yang membuat produk benar-benar bisa punya BANYAK foto.
        // Primary hanya di-set otomatis di sini bila produk belum punya foto sama
        // sekali (mis. produk baru pertama kali diisi foto lewat form Edit);
        // selain itu Admin mengatur foto utama lewat radio "Jadikan Utama" di atas.
        if ($request->hasFile('images')) {
            $hasAnyImage = $product->images()->exists();
            foreach (array_values($request->file('images')) as $index => $img) {
                $path = $img->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => !$hasAnyImage && $index === 0,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk dan stok berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $product->images()->delete();
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}