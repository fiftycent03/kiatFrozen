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
            'price_per_kg' => 'required|numeric|min:0',
            'min_pembelian'=> 'required|numeric|min:1',
            'stock'        => 'required|numeric|min:0', // Menambahkan validasi stok
            'satuan'       => 'required|in:kg,pcs',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
            'images.*'     => 'image|max:2048'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->images as $i => $img) {
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
        $product->load('images');
        return view('admin.product-edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'sku_root'     => 'required|unique:products,sku_root,'.$product->id,
            'price_per_kg' => 'required|numeric|min:0',
            'min_pembelian'=> 'required|numeric|min:1',
            'stock'        => 'required|numeric|min:0', // Menambahkan validasi stok saat update
            'satuan'       => 'required|in:kg,pcs',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
            'images.*'     => 'image|max:2048',
            'delete_images'=> 'array',
            'primary_image_id' => 'nullable|exists:product_images,id'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $product->update($data);

        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $id) {
                $img = ProductImage::find($id);
                if ($img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }
        }

        if ($request->filled('primary_image_id')) {
            ProductImage::where('product_id', $product->id)
                ->update(['is_primary' => false]);

            ProductImage::where('id', $request->primary_image_id)
                ->update(['is_primary' => true]);
        }

        // === PERBAIKAN BUG GANTI GAMBAR SAAT EDIT ===
        // Bug lama: gambar baru disimpan dengan is_primary=false, sehingga foto utama
        // (primaryImage) yang tampil di katalog TIDAK pernah berubah -> terlihat seperti
        // "upload tidak tersimpan". Sekarang: gambar pertama yang diupload menjadi foto
        // UTAMA (replace), dan foto utama lama dihapus dari storage + database.
        if ($request->hasFile('images')) {
            // Simpan referensi foto utama lama untuk dihapus setelah pengganti tersimpan.
            $oldPrimary = $product->images()->where('is_primary', true)->first();

            foreach (array_values($request->file('images')) as $index => $img) {
                // Simpan file fisik baru ke storage/app/public/products
                $path = $img->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    // Gambar pertama jadi foto utama (mengganti yang lama),
                    // gambar berikutnya (jika upload banyak) masuk sebagai galeri.
                    'is_primary' => $index === 0,
                ]);
            }

            // Hapus file + record foto utama lama agar tidak menumpuk & benar-benar terganti.
            if ($oldPrimary) {
                Storage::disk('public')->delete($oldPrimary->path);
                $oldPrimary->delete();
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