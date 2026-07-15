<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class KatalogController extends Controller
{
    public function index(Request $request, $kategori = null)
    {
        // 1. Mulai Query
        // 'variants' di-eager-load karena kartu katalog kini menampilkan jumlah
        // varian & harga "mulai dari" untuk produk unit_type='kg' (hindari N+1).
        $query = Product::with(['primaryImage', 'variants'])->where('is_active', 1);

        // 2. Filter Kategori (Jika ada di URL).
        // BUG LAMA: $kategori berisi SLUG kategori (mis. "japanes"), tapi kode lama
        // mencocokkannya ke kolom `name` produk -> hampir selalu tidak match -> kosong.
        // PERBAIKAN: cari Category berdasarkan slug, lalu filter produk lewat
        // relasi category_id (Product belongsTo Category, lihat app/Models/Product.php).
        if ($kategori) {
            $category = Category::where('slug', $kategori)->first();

            // Filter produk berdasarkan category_id milik kategori yang ditemukan.
            // Jika slug tidak ditemukan, where('category_id', null) akan menghasilkan
            // hasil kosong secara aman (bukan error 500).
            $query->where('category_id', $category?->id);
        }

        // 3. Filter Search (Manual via Form Submit)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 4. Ambil Data
        $products = $query->latest()->get();

        // 5. Ambil daftar kategori AKTIF dari database untuk sidebar.
        // Sebelumnya sidebar memakai daftar hardcoded yang tidak sinkron dengan
        // kategori dinamis yang dibuat Admin (mis. "Japanes"). Sekarang dibaca
        // langsung dari tabel categories agar selalu konsisten dengan data nyata.
        $categories = Category::where('is_active', 1)->orderBy('name')->get();

        // 6. Kembalikan View Utama
        return view('user.katalog', compact('products', 'kategori', 'categories'));
    }

    /**
     * Halaman Detail Produk — galeri foto (maks 5) + logika kondisional Pcs/Kg:
     * - unit_type='pcs' : tampil harga & stok utama langsung, tombol Add to Cart aktif.
     * - unit_type='kg'  : user WAJIB memilih salah satu Varian Potongan/Gramasi
     *                     dulu (harga & stok mengikuti varian yang diklik) sebelum
     *                     tombol Add to Cart aktif — lihat resources/views/user/produk-detail.blade.php.
     */
    public function show(Product $product)
    {
        // Produk nonaktif tidak boleh diakses langsung lewat URL detail.
        abort_unless($product->is_active, 404);

        $product->load(['images', 'variants', 'category']);

        return view('user.produk-detail', compact('product'));
    }
}