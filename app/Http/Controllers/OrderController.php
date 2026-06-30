<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    private function getCart() {
        return session()->get('cart', []);
    }

    private function saveCart($cart) {
        session(['cart' => $cart]);
    }

    public function index() {
        return $this->create();
    }

    public function create() {
        $cart  = $this->getCart();
        $user = Auth::user();
        $total = collect($cart)->sum('subtotal');
        
        $addresses = $user ? $user->addresses : collect();

        if (empty($cart)) {
            $selectedAddress = null;
            return view('user.cart', compact('cart', 'total', 'addresses', 'selectedAddress'));
        }

        // Logika Alamat Terakhir.
        // Guard $user: untuk GUEST (belum login) tidak ada riwayat order/alamat tersimpan,
        // jadi jangan akses $user->id / $user->addresses() yang akan error pada null.
        $lastOrder = $user ? Order::where('user_id', $user->id)->latest()->first() : null;

        if ($lastOrder) {
            // Pakai alamat dari pesanan terakhir user sebagai isian default.
            $selectedAddress = (object) [
                'customer_name' => $lastOrder->customer_name,
                'customer_phone' => $lastOrder->customer_phone,
                'province' => $lastOrder->province,
                'city' => $lastOrder->city,
                'district' => $lastOrder->district,
                'address_detail' => $lastOrder->customer_address
            ];
        } elseif ($user) {
            // User login tapi belum pernah order: ambil alamat default dari buku alamat.
            $selectedAddress = $user->addresses()->where('is_default', true)->first()
                               ?? $user->addresses()->first();
        } else {
            // Guest: form alamat dibiarkan kosong untuk diisi manual saat checkout.
            $selectedAddress = null;
        }

        return view('user.cart', compact('cart', 'total', 'addresses', 'selectedAddress'));
    }

    public function store(Request $request) {
        $request->validate([
            'customer_name'  => 'required',
            'customer_phone' => 'required',
            'province'       => 'required',
            'city'           => 'required',
            'district'       => 'required',
            'address_detail' => 'required',
        ]);

        $cart = $this->getCart();

        // Ongkos kirim di-hardcode 0 — validasi via tabel shipping_rates dihapus agar
        // semua wilayah (termasuk luar kota) bisa checkout dan membayar via Midtrans.
        $finalShippingFee = 0;
        $subtotal = collect($cart)->sum(fn($i) => $i['subtotal']);
        $isAjax   = $request->ajax() || $request->expectsJson();

        $order = DB::transaction(function () use ($cart, $request, $subtotal, $finalShippingFee) {
            $order = Order::create([
                'user_id'            => Auth::id(),
                'code'               => 'ORD-' . strtoupper(Str::random(6)),
                'payment_status'     => 'pending',
                'fulfillment_status' => 'pending',
                'subtotal'           => $subtotal,
                'shipping_fee'       => $finalShippingFee,
                'shipping_service'   => 'standard',
                'total'              => $subtotal + $finalShippingFee,
                'shipping_date'      => now(),
                'customer_name'      => $request->customer_name,
                'customer_phone'     => $request->customer_phone,
                'customer_address'   => $request->address_detail,
                'province'           => $request->province,
                'city'               => $request->city,
                'district'           => $request->district,
                // Satu-satunya metode pembayaran: Midtrans Snap.
                'payment_channel'    => 'midtrans',
                'notes'              => $request->notes,
            ]);

            foreach ($cart as $item) {
                $product    = Product::find($item['product_id']);
                $totalGrams = $product ? ($product->grams * $item['qty']) : 0;

                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $item['product_id'],
                    'qty'                   => $item['qty'],
                    'subtotal'              => $item['subtotal'],
                    'name_snapshot'         => $item['name'],
                    'price_per_kg_snapshot' => $item['price'],
                    'grams'                 => $totalGrams,
                ]);

                // Pengurangan stok otomatis (atomik di dalam transaksi).
                Product::where('id', $item['product_id'])->decrement('stock', $item['qty']);
            }

            session()->forget('cart');
            return $order;
        });

        // Generate Snap Token Midtrans setelah order & items tersimpan.
        $snapToken = $this->generateSnapToken($order->load('items'));

        // Bila request dari AJAX ($.ajax di cart.blade.php), balas JSON berisi token.
        if ($isAjax) {
            return response()->json([
                'success'     => true,
                'snap_token'  => $snapToken,
                'order_id'    => $order->id,
                'success_url' => route('order.success', $order->id),
            ]);
        }

        return redirect()->route('order.success', $order->id);
    }

    public function confirmDelivered($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Keamanan: tombol konfirmasi hanya boleh diproses jika kurir sudah upload
        // bukti pengiriman (delivery_proof tidak null) dan status sudah 'delivered'.
        // Validasi ganda ini mencegah manipulasi URL langsung oleh user.
        if ($order->fulfillment_status !== 'delivered' || !$order->delivery_proof) {
            return back()->with('error', 'Konfirmasi belum bisa dilakukan: kurir belum mengunggah bukti pengiriman.');
        }

        // Set status ke 'received' (bukan 'delivered') agar bisa dibedakan:
        // 'delivered' = kurir sudah antar + ada bukti foto.
        // 'received'  = customer sudah konfirmasi terima barang.
        $order->update(['fulfillment_status' => 'received']);

        return back()->with('success', 'Terima kasih! Penerimaan pesanan berhasil dikonfirmasi.');
    }

    public function success($id) {
        $order = Order::findOrFail($id);

        // Buat Snap Token Midtrans untuk pembayaran online di halaman sukses.
        $snapToken = $this->generateSnapToken($order);

        return view('user.orderSuccess', compact('order', 'snapToken'));
    }

    /**
     * Membuat Snap Token Midtrans dari data order.
     *
     * Mengembalikan null bila paket midtrans/midtrans-php belum diinstall ATAU
     * kredensial (server key) belum diisi di .env — sehingga halaman tetap dapat
     * dibuka dengan fallback pembayaran transfer manual.
     */
    private function generateSnapToken(Order $order)
    {
        // Pengaman sebelum `composer require midtrans/midtrans-php`: cek class & key dulu.
        if (!class_exists(\Midtrans\Snap::class) || !config('services.midtrans.server_key')) {
            return null;
        }

        // 1) Set kredensial Midtrans dari config/services.php
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized  = (bool) config('services.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds        = (bool) config('services.midtrans.is_3ds');

        // 2) Susun item_details: satu entri per baris order (price = subtotal baris, qty = 1)
        //    agar total mudah diverifikasi Midtrans tanpa masalah pembulatan.
        $itemDetails = [];
        if ($order->relationLoaded('items')) {
            $itemDetails = $order->items->map(fn($i) => [
                'id'       => 'PROD-' . ($i->product_id ?? 0),
                'price'    => (int) $i->subtotal,
                'quantity' => 1,
                'name'     => mb_substr($i->name_snapshot, 0, 50),
            ])->toArray();
        }

        // 3) Susun parameter transaksi: detail order + data customer + rincian item
        $params = [
            'transaction_details' => [
                'order_id'     => $order->code,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone'      => $order->customer_phone,
            ],
            'item_details' => $itemDetails,
        ];

        // 3) Minta Snap Token. Bila gagal (mis. key salah), kembalikan null tanpa menggagalkan halaman.
        try {
            return \Midtrans\Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Endpoint konfirmasi pembayaran Midtrans dari browser user.
     *
     * Dipanggil oleh callback onSuccess Snap.js di cart.blade.php setelah
     * user berhasil bayar. Karena localhost tidak bisa ditembus webhook Midtrans
     * dari luar, kita andalkan callback frontend sebagai pengganti sementara.
     *
     * Alur: Snap popup sukses → JS fetch POST ke sini → status jadi 'paid'
     *       → notifikasi dikirim ke semua admin → JS redirect ke halaman sukses.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:orders,id']);

        $order = Order::findOrFail($request->order_id);

        // Hanya update sekali — cegah perubahan duplikat bila endpoint dipanggil ulang.
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            // Kirim notifikasi database ke semua user dengan role 'admin'
            // agar segera terlihat di badge lonceng dashboard admin.
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewOrderNotification($order));
        }

        return response()->json(['success' => true]);
    }

    public function show($id) {
        $order = Order::with('items.product')->findOrFail($id);
        return view('user.orderDetail', compact('order'));
    }

    public function uploadProof(Request $request, $id) {
        $request->validate(['payment_proof' => 'required|image|max:2048']);
        $order = Order::findOrFail($id);
        if ($request->hasFile('payment_proof')) {
            if ($order->payment_proof) Storage::disk('public')->delete($order->payment_proof);
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $order->update(['payment_proof' => $path]);
        }
        return back()->with('success', 'Bukti berhasil diunggah.');
    }

    public function getCities($province) { 
        return response()->json(DB::table('shipping_rates')->where('province_name', urldecode($province))->distinct()->get(['city_name'])); 
    }
    
    public function getDistricts($city) { 
        return response()->json(DB::table('shipping_rates')->where('city_name', urldecode($city))->distinct()->get(['district_name'])); 
    }
    
    public function getShippingCost(Request $request) {
        $rate = DB::table('shipping_rates')->where('province_name', $request->province)->where('city_name', $request->city)->where('district_name', $request->district)->first();
        return $rate ? response()->json(['success' => true, 'cost' => (int) $rate->cost]) : response()->json(['success' => false]);
    }
}