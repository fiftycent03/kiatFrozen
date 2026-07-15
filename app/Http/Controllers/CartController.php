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

    // Snapshot ringkas isi keranjang untuk dikirim sebagai JSON ke Mini Cart Drawer.
    // Bentuknya: jumlah jenis produk (count), subtotal, dan daftar item (values saja
    // agar mudah di-loop di sisi JavaScript).
    private function cartSnapshot(): array
    {
        $cart = $this->getCart();
        return [
            'count'    => count($cart),
            'subtotal' => (int) collect($cart)->sum('subtotal'),
            'items'    => array_values($cart),
        ];
    }

    // Endpoint JSON: dipanggil Mini Cart Drawer (fetch) untuk mengambil isi keranjang
    // terbaru — dipakai saat halaman dimuat & sebagai penyegar setelah tiap perubahan.
    public function data()
    {
        return response()->json($this->cartSnapshot());
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
            // variant_id dikirim oleh Halaman Detail Produk HANYA untuk produk
            // unit_type='kg' setelah user mengklik salah satu tombol varian.
            'variant_id' => 'nullable|exists:product_variants,id',
            'qty'        => 'nullable|integer|min:1',
        ]);

        $product = Product::with('primaryImage')->findOrFail($data['product_id']);
        $isAjax  = $request->ajax() || $request->expectsJson();

        // ------------------------------------------------------------------
        // LOGIKA IF/ELSE PCS vs KG (sisi keranjang):
        // - variant_id ADA  -> pakai harga & stok VARIAN (cabang Kg).
        // - variant_id TIDAK ADA -> pakai harga & stok UTAMA produk (cabang Pcs,
        //   sama persis seperti sebelum fitur varian ditambahkan).
        // ------------------------------------------------------------------
        $variant = null;
        if (!empty($data['variant_id'])) {
            // find() lewat relasi product->variants() memastikan varian ini
            // benar-benar milik produk yang dimaksud (tidak bisa dipalsukan
            // dari produk lain lewat ID acak).
            $variant = $product->variants()->find($data['variant_id']);
            if (!$variant) {
                $msg = 'Varian tidak ditemukan untuk produk ini.';
                if ($isAjax) return response()->json(['success' => false, 'message' => $msg], 422);
                return back()->with('error', $msg);
            }
        }

        if ($variant) {
            // ---------------- CABANG KG (varian terpilih) ----------------
            $minBeli = 1; // qty berarti "berapa unit varian ini", bukan berat
            $qty = $data['qty'] ?? $minBeli;

            if ($qty > $variant->stock) {
                $msg = "Stok untuk varian \"{$variant->label}\" tidak mencukupi (tersisa {$variant->stock}).";
                if ($isAjax) return response()->json(['success' => false, 'message' => $msg], 422);
                return back()->with('error', $msg);
            }

            // Key KOMPOSIT "productId_variantId" -> varian berbeda dari produk
            // yang sama tersimpan sebagai baris keranjang yang TERPISAH.
            $cartKey = $product->id . '_' . $variant->id;
            $cart = $this->getCart();

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['qty'] += $qty;
            } else {
                $cart[$cartKey] = [
                    'product_id'    => $product->id,
                    'variant_id'    => $variant->id,
                    // Nama gabungan produk+varian dipakai APA ADANYA oleh semua
                    // halaman lama (cart/checkout/order) yang hanya baca key
                    // 'name' — tidak perlu ubah template mana pun untuk itu.
                    'name'          => "{$product->name} ({$variant->label})",
                    'price'         => $variant->price,
                    'qty'           => $qty,
                    'min_pembelian' => $minBeli,
                    'satuan'        => 'pcs', // qty dihitung per-unit varian
                    'image'         => $product->primaryImage?->path,
                ];
            }
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['price'] * $cart[$cartKey]['qty'];
            $this->saveCart($cart);
        } else {
            // ---------------- CABANG PCS (tidak berubah) ----------------
            $minBeli = $product->min_pembelian ?? 1;
            $qty = $data['qty'] ?? $minBeli;

            if ($qty < $minBeli) {
                $msg = "Minimal pembelian adalah {$minBeli} {$product->satuan}.";
                if ($isAjax) return response()->json(['success' => false, 'message' => $msg], 422);
                return back()->with('error', $msg);
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
        }

        // AJAX (tombol quick-add di Mini Cart Drawer / Halaman Detail Produk):
        // balas JSON berisi snapshot keranjang terbaru agar drawer & badge
        // ter-update TANPA reload halaman.
        if ($isAjax) {
            return response()->json(array_merge(
                ['success' => true, 'message' => 'Produk ditambahkan ke keranjang.'],
                $this->cartSnapshot()
            ));
        }

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
            // Baris VARIAN (unit_type='kg') selalu terkunci minimal 1 unit,
            // TIDAK memakai min_pembelian milik produk induk — kolom itu
            // adalah aturan minimal untuk mode Pcs, bukan untuk jumlah unit
            // varian potongan yang dipilih di Halaman Detail Produk.
            $isVariantLine = isset($cart[$id]['variant_id']);
            $product = Product::find($cart[$id]['product_id']);
            // Cast (int) agar perbandingan minimal selalu numerik, bukan string.
            $minOrder   = $isVariantLine ? 1 : (int) ($product ? ($product->min_pembelian ?? 1) : 1);
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

    public function remove(Request $request, $productId)
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);
        }

        // AJAX (tombol hapus di Mini Cart Drawer): balas JSON snapshot terbaru.
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(array_merge(['success' => true], $this->cartSnapshot()));
        }

        return back()->with('success', 'Produk dihapus.');
    }
}