<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function getCart() {
        return session()->get('cart', []);
    }

    private function saveCart($cart) {
        session(['cart' => $cart]);
    }

    public function index() {
        $cart  = $this->getCart();
        $total = collect($cart)->sum('subtotal');
        $cartCount = count($cart); 

        $provinces = DB::table('shipping_rates')->distinct()->pluck('province_name');

        return view('user.cart', compact('cart', 'total', 'cartCount', 'provinces'));
    }

    public function add(Request $request) {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'nullable|integer',
        ]);

        $product = Product::with('primaryImage')->findOrFail($data['product_id']);
        
        // Tetap gunakan min_pembelian dari database
        $minBeli = $product->min_pembelian ?? 1; 
        $qty = $data['qty'] ?? $minBeli;

        if ($qty < $minBeli) {
            return back()->with('error', "Minimal pembelian adalah {$minBeli} {$product->satuan}.");
        }

        $cart = $this->getCart();

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
        } else {
            $cart[$product->id] = [
                'product_id'    => $product->id,
                'name'          => $product->name,
                'price'         => $product->price_per_kg,
                'qty'           => $qty,
                'min_pembelian' => $minBeli, 
                'satuan'        => $product->satuan ?? 'Kg',
                'image'         => $product->primaryImage?->path,
            ];
        }

        $cart[$product->id]['subtotal'] = $cart[$product->id]['price'] * $cart[$product->id]['qty'];
        $this->saveCart($cart);

        // PERBAIKAN: Menggunakan back() agar tetap di katalog, bukan redirect ke cart
        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function updateQty(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        // Apakah request datang dari AJAX (tombol +/- di halaman keranjang)?
        // Jika ya, kita balas JSON agar halaman TIDAK perlu reload penuh.
        $isAjax = $request->ajax() || $request->expectsJson();

        if (isset($cart[$id])) {
            $product = Product::find($cart[$id]['product_id']);
            // Cast (int) agar perbandingan minimal selalu numerik, bukan string.
            $minOrder   = (int) ($product ? ($product->min_pembelian ?? 1) : 1);
            $currentQty = (int) $cart[$id]['qty'];

            if ($request->action == 'increase') {
                $cart[$id]['qty']++;
            } elseif ($request->action == 'decrease') {
                // Logika kunci minimal pembelian: tolak bila sudah di batas minimum.
                if ($currentQty <= $minOrder) {
                    $msg = "Minimal pemesanan adalah {$minOrder} {$cart[$id]['satuan']}.";
                    // AJAX: balas JSON gagal (status 200) tanpa redirect agar tombol tidak reload.
                    if ($isAjax) {
                        return response()->json([
                            'success'       => false,
                            'qty'           => $currentQty,
                            'min_pembelian' => $minOrder,
                            'at_min'        => true,
                            'message'       => $msg,
                        ]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $cart[$id]['qty']--;
            }

            $cart[$id]['subtotal'] = $cart[$id]['price'] * $cart[$id]['qty'];
            session()->put('cart', $cart);

            // Total seluruh keranjang setelah perubahan (untuk update panel checkout via JS).
            $grandTotal = collect($cart)->sum('subtotal');
            $newQty     = (int) $cart[$id]['qty'];

            // AJAX: kirim data terbaru agar JS bisa update DOM tanpa reload.
            if ($isAjax) {
                return response()->json([
                    'success'       => true,
                    'qty'           => $newQty,
                    'subtotal'      => $cart[$id]['subtotal'],
                    'grand_total'   => $grandTotal,
                    'min_pembelian' => $minOrder,
                    // at_min dipakai JS untuk menonaktifkan tombol "-" & menampilkan peringatan.
                    'at_min'        => $newQty <= $minOrder,
                ]);
            }

            return redirect()->back()->with('success', 'Jumlah diperbarui.');
        }

        // Item tidak ditemukan di keranjang.
        if ($isAjax) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }
        return redirect()->back()->with('error', 'Produk tidak ditemukan.');
    }

    public function buyNow(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'nullable|integer',
        ]);

        $product = Product::with('primaryImage')->findOrFail($data['product_id']);
        $minBeli = $product->min_pembelian ?? 1;
        $qty = $data['qty'] ?? $minBeli;

        session()->forget('cart');

        $cart[$product->id] = [
            'product_id'    => $product->id,
            'name'          => $product->name,
            'price'         => $product->price_per_kg,
            'qty'           => $qty,
            'min_pembelian' => $minBeli,
            'satuan'        => $product->satuan ?? 'Kg',
            'image'         => $product->primaryImage?->path,
            'subtotal'      => $product->price_per_kg * $qty
        ];

        $this->saveCart($cart);
        
        // Khusus Beli Langsung tetap redirect ke checkout
        return redirect()->route('checkout.index');
    }

    public function remove($productId)
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);
        }
        return back()->with('success', 'Produk dihapus.');
    }
}