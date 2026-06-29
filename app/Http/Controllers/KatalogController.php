<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class KatalogController extends Controller
{
    public function index(Request $request, $kategori = null)
    {
        // 1. Mulai Query
        $query = Product::where('is_active', 1);

        // 2. Filter Kategori (Jika ada di URL)
        if ($kategori) {
            $query->where('name', 'like', '%' . $kategori . '%');
        }

        // 3. Filter Search (Manual via Form Submit)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 4. Ambil Data
        $products = $query->latest()->get();

        // 5. Kembalikan View Utama
        return view('user.katalog', compact('products', 'kategori'));
    }
}