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

                // PENGURANGAN STOK DIPINDAHKAN ke confirmPayment() (dipanggil saat
                // onSuccess/onPending Midtrans). Order di titik ini baru "niat checkout" —
                // belum tentu dibayar. Stok baru dikurangi setelah pembayaran benar-benar
                // diproses, supaya order yang akhirnya tidak pernah dibayar tidak membuat
                // stok produk lain "hangus" secara salah.
            }

            // Alur "Pay Later": begitu order terbentuk, dia SAH dan akan tetap ada di
            // sistem (muncul di Riwayat Pesanan) terlepas dari popup Midtrans jadi
            // dibayar sekarang atau nanti — makanya cart dikosongkan DI SINI, bukan
            // ditunda sampai pembayaran sukses. Kalau user menutup popup (onClose),
            // order TIDAK dihapus — dia tinggal buka lagi lewat halaman Order Detail.
            session()->forget('cart');

            return $order;
        });

        // Generate Snap Token Midtrans setelah order & items tersimpan, LALU simpan
        // ke kolom snap_token pada row order tsb. Token inilah yang nanti dipakai
        // ulang oleh tombol "Lanjutkan Pembayaran" di halaman Order Detail — supaya
        // popup yang dibuka kembali adalah SESI PEMBAYARAN YANG SAMA (bukan transaksi
        // baru tiap kali di-generate ulang).
        $snapToken = $this->generateSnapToken($order->load('items'));
        if ($snapToken) {
            $order->update(['snap_token' => $snapToken]);
        }

        // Bila request dari AJAX ($.ajax di cart.blade.php), balas JSON berisi token.
        if ($isAjax) {
            return response()->json([
                'success'     => true,
                'snap_token'  => $snapToken,
                'order_id'    => $order->id,
                'success_url' => route('order.success', $order->id),
                'detail_url'  => route('order.show', $order->id),
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
        $order = Order::with('items')->findOrFail($id);

        // Pakai snap_token yang sudah tersimpan dari saat checkout (store()) — JANGAN
        // generate baru di sini. Meminta token baru untuk order.code yang sama akan
        // membuat sesi transaksi Midtrans terpisah dari yang barusan dibuka user.
        // Fallback: generate + simpan hanya jika token belum ada sama sekali
        // (mis. order lama sebelum kolom snap_token ditambahkan).
        if (!$order->snap_token && $order->payment_channel === 'midtrans' && $order->payment_status !== 'paid') {
            $order->update(['snap_token' => $this->generateSnapToken($order)]);
        }

        return view('user.orderSuccess', compact('order'));
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
     * Dipanggil oleh callback onSuccess ATAU onPending Snap.js (cart.blade.php,
     * orderSuccess.blade.php, orderDetail.blade.php) setelah popup Midtrans
     * memberi hasil. Karena localhost tidak bisa ditembus webhook Midtrans
     * dari luar, kita andalkan callback frontend sebagai pengganti sementara.
     *
     * Parameter 'status' membedakan dua kasus:
     * - 'paid'    (dari onSuccess) → payment_status langsung 'paid'.
     * - 'pending' (dari onPending, mis. VA/QR belum ditransfer) → payment_status
     *              TETAP 'pending', tapi stok tetap "dikunci" di sini karena order
     *              sudah pasti akan diproses sebentar lagi.
     *
     * PEMINDAHAN LOGIKA STOK: Pengurangan stok yang tadinya ada di store() (langsung
     * saat klik checkout) dipindahkan ke sini. stock_reserved_at dipakai sebagai
     * penanda idempoten agar retry pembayaran (mis. via tombol "Lanjutkan Pembayaran"
     * di orderDetail, yang memakai ULANG snap_token yang sama) TIDAK mengurangi
     * stok dua kali untuk order yang sama.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status'   => 'nullable|in:paid,pending',
        ]);

        $order  = Order::with('items')->findOrFail($request->order_id);
        $status = $request->input('status', 'paid');

        // Kurangi stok HANYA SEKALI per order (dicek via stock_reserved_at), baik
        // dipicu dari onSuccess maupun onPending. Ini mencegah pengurangan ganda saat
        // user retry bayar (mis. onPending dulu lalu berhasil onSuccess di percobaan
        // berikutnya lewat tombol "Lanjutkan Pembayaran"). Cart TIDAK disentuh di sini
        // lagi — sudah dikosongkan sejak order dibuat di store() (alur "Pay Later").
        if (!$order->stock_reserved_at) {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->decrement('stock', $item->qty);
            }
            $order->stock_reserved_at = now();
        }

        // Hanya set 'paid' sekali — cegah perubahan/notifikasi duplikat bila endpoint
        // dipanggil ulang (mis. onSuccess menyusul onPending untuk order yang sama).
        if ($status === 'paid' && $order->payment_status !== 'paid') {
            $order->payment_status = 'paid';
            $order->paid_at        = now();

            // Kirim notifikasi database ke semua user dengan role 'admin'
            // agar segera terlihat di badge lonceng dashboard admin.
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewOrderNotification($order));
        }

        $order->save();

        return response()->json(['success' => true]);
    }

    public function show($id) {
        $order = Order::with('items.product')->findOrFail($id);

        // ALUR "PAY LATER": order TIDAK PERNAH dihapus meski popup Midtrans sempat
        // ditutup tanpa bayar (lihat onClose di cart.blade.php) — jadi di sini kita
        // tinggal pastikan snap_token tersedia untuk tombol "Lanjutkan Pembayaran".
        // Pakai token yang SUDAH TERSIMPAN dari saat checkout; generate baru HANYA
        // sebagai fallback bila order lama belum punya snap_token sama sekali.
        if ($order->payment_channel === 'midtrans' && $order->payment_status !== 'paid' && !$order->snap_token) {
            $order->update(['snap_token' => $this->generateSnapToken($order)]);
        }

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