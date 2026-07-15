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
        // semua wilayah (termasuk luar kota) bisa checkout.
        $finalShippingFee = 0;
        $subtotal = collect($cart)->sum(fn($i) => $i['subtotal']);

        $order = DB::transaction(function () use ($cart, $request, $subtotal, $finalShippingFee) {
            $order = Order::create([
                'user_id'            => Auth::id(),
                'code'               => 'ORD-' . strtoupper(Str::random(6)),
                // Order baru SELALU mulai dari 'pending' — baru berubah jadi
                // 'awaiting_verification' setelah user upload bukti transfer
                // (lihat uploadProof()), lalu 'paid'/'rejected' setelah Admin
                // memverifikasi (lihat Admin\OrderController@approvePayment/rejectPayment).
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
                // Satu-satunya metode pembayaran sekarang: Transfer Bank Manual.
                // (Midtrans sudah dihapus total dari alur checkout.)
                'payment_channel'    => 'transfer',
                'notes'              => $request->notes,
            ]);

            foreach ($cart as $item) {
                $product    = Product::find($item['product_id']);
                $totalGrams = $product ? ($product->grams * $item['qty']) : 0;

                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $item['product_id'],
                    // variant_id/label hanya terisi untuk baris keranjang produk
                    // unit_type='kg' (lihat CartController@add) — null untuk Pcs.
                    // Disimpan sebagai snapshot agar riwayat pesanan tetap utuh
                    // meski varian aslinya kelak diedit/dihapus Admin.
                    'variant_id'            => $item['variant_id'] ?? null,
                    'variant_label_snapshot'=> isset($item['variant_id']) ? $item['name'] : null,
                    'qty'                   => $item['qty'],
                    'subtotal'              => $item['subtotal'],
                    'name_snapshot'         => $item['name'],
                    'price_per_kg_snapshot' => $item['price'],
                    'grams'                 => $totalGrams,
                ]);

                // PENGURANGAN STOK TIDAK terjadi di sini. Order di titik ini baru
                // "niat checkout" — pembayaran belum diverifikasi sama sekali.
                // Stok baru dikurangi setelah Admin klik "ACC Pembayaran" (lihat
                // Admin\OrderController@approvePayment), supaya order yang bukti
                // transfernya ditolak/tidak pernah di-upload tidak membuat stok
                // produk lain "hangus" secara salah.
            }

            // Order tetap SAH begitu terbentuk (muncul di Riwayat Pesanan) meskipun
            // belum dibayar — makanya cart dikosongkan DI SINI. User tinggal upload
            // bukti transfer kapan saja lewat halaman Order Detail.
            session()->forget('cart');

            return $order;
        });

        // Transfer Bank Manual: tidak ada payment gateway untuk dipanggil di sini.
        // Langsung arahkan ke halaman sukses — di sana ditampilkan info rekening
        // tujuan transfer + form upload bukti bayar.
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

        // Halaman sukses checkout: tampilkan info rekening tujuan transfer +
        // form upload bukti bayar (lihat resources/views/user/orderSuccess.blade.php).
        // Tidak ada payment gateway untuk disiapkan di sini lagi.
        return view('user.orderSuccess', compact('order'));
    }

    public function show($id) {
        $order = Order::with('items.product')->findOrFail($id);

        // Order TIDAK PERNAH dihapus walau belum dibayar — user bisa kembali ke
        // halaman ini kapan saja untuk upload/lihat status bukti transfernya.
        return view('user.orderDetail', compact('order'));
    }

    /**
     * Terima unggahan bukti transfer dari user, simpan file-nya, lalu ubah
     * payment_status menjadi 'awaiting_verification' — menandakan pesanan
     * sedang menunggu ditinjau Admin (lihat Admin\OrderController@approvePayment
     * & @rejectPayment untuk langkah selanjutnya).
     */
    public function uploadProof(Request $request, $id) {
        $request->validate(['payment_proof' => 'required|image|max:2048']);
        $order = Order::findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            // Ganti file lama (mis. dari upload sebelumnya yang ditolak Admin)
            // agar storage tidak menumpuk file bukti yang sudah tidak relevan.
            if ($order->payment_proof) Storage::disk('public')->delete($order->payment_proof);
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            $order->update([
                'payment_proof'  => $path,
                // LOGIKA STATUS: begitu bukti transfer masuk, pesanan pindah dari
                // 'pending'/'rejected' -> 'awaiting_verification'. Stok BELUM
                // dikurangi di sini — baru dikurangi saat Admin klik "ACC Pembayaran".
                'payment_status' => 'awaiting_verification',
            ]);

            // Notifikasi database ke semua Admin: sebelumnya dikirim saat Midtrans
            // konfirmasi 'paid', kini dipindah ke sini — momen paling relevan untuk
            // Admin di alur manual (ada bukti baru yang perlu segera ditinjau).
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewOrderNotification($order));
        }

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi Admin.');
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