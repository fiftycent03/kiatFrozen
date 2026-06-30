<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Halaman kelola kategori (list + form upload gambar).
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category-index', compact('categories'));
    }

    /**
     * Simpan kategori baru.
     *
     * Dipakai untuk DUA jalur sekaligus:
     * 1) Form biasa di halaman kelola kategori (dengan upload gambar).
     * 2) Tambah kategori INLINE via AJAX dari form Tambah Produk (mengembalikan JSON).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // gambar opsional (maks 2MB)
        ]);

        $category = new Category();
        $category->name = $data['name'];
        // Slug dibuat otomatis dari nama, dijamin unik dengan menambahkan id bila perlu.
        $category->slug = Str::slug($data['name']);
        $category->is_active = true;

        // Jika admin mengupload gambar, simpan ke storage/app/public/categories.
        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('categories', 'public');
        }

        $category->save();

        // Jika permintaan datang dari AJAX (form tambah produk) -> balas JSON,
        // sehingga dropdown kategori bisa langsung diisi tanpa pindah halaman.
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'id'      => $category->id,
                'name'    => $category->name,
            ]);
        }

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Update kategori (terutama untuk mengganti gambar kategori).
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'image'     => 'nullable|image|max:2048',
        ]);

        $category->name = $data['name'];
        $category->slug = Str::slug($data['name']);
        $category->is_active = $request->has('is_active') ? 1 : 0;

        // Ganti gambar: hapus file lama lebih dulu agar tidak menumpuk di storage.
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $category->image = $request->file('image')->store('categories', 'public');
        }

        $category->save();

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori beserta file gambarnya.
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
