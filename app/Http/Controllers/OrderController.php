<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
            'customer_name'    => 'required',
            'customer_phone'   => 'required',
            'province'         => 'required',
            'city'             => 'required',
            'district'         => 'required',
            'address_detail'   => 'required',
            'payment_method'   => 'required',
            'shipping_service' => 'required',
        ]);

        $cart = $this->getCart();
        
        $shippingRate = DB::table('shipping_rates')
            ->where('province_name', $request->province)
            ->where('city_name', $request->city)
            ->where('district_name', $request->district)
            ->first();

        if (!$shippingRate) return back()->with('error', 'Wilayah tidak terjangkau.');

        $serviceExtra = ($request->shipping_service === 'express') ? 5000 : 0;
        $finalShippingFee = $shippingRate->cost + $serviceExtra;
        $subtotal = collect($cart)->sum(fn($i) => $i['subtotal']);

        return DB::transaction(function () use ($cart, $request, $subtotal, $finalShippingFee) {
            $order = Order::create([
                'user_id'            => Auth::id(),
                'code'               => 'ORD-' . strtoupper(Str::random(6)),
                'payment_status'     => 'pending',
                'fulfillment_status' => 'pending',
                'subtotal'           => $subtotal,
                'shipping_fee'       => $finalShippingFee,
                'shipping_service'   => $request->shipping_service,
                'total'              => $subtotal + $finalShippingFee,
                'shipping_date'      => now(),
                'customer_name'      => $request->customer_name,
                'customer_phone'     => $request->customer_phone,
                'customer_address'   => $request->address_detail,
                'province'           => $request->province,
                'city'               => $request->city,
                'district'           => $request->district,
                'payment_channel'    => $request->payment_method,
                'notes'              => $request->notes,
            ]);

            foreach ($cart as $item) {
                // Mencari data produk asli untuk mengambil nilai berat ('grams')
                $product = Product::find($item['product_id']);
                
                // Menghitung total berat per item berdasarkan jumlah quantity yang dibeli
                $totalGrams = $product ? ($product->grams * $item['qty']) : 0;

                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $item['product_id'],
                    'qty'                   => $item['qty'],
                    'subtotal'              => $item['subtotal'],
                    'name_snapshot'         => $item['name'],
                    'price_per_kg_snapshot' => $item['price'],
                    'grams'                 => $totalGrams, // Data grams dimasukkan ke database di sini
                ]);
                
                Product::where('id', $item['product_id'])->decrement('stock', $item['qty']);
            }

            session()->forget('cart');
            return redirect()->route('order.success', $order->id);
        });
    }

    public function confirmDelivered($id)
    {
        // Jam Operasional: 09:00 - 17:00
        $jamSekarang = now()->format('H:i');
        $hariIni = now()->format('N'); 
        $jamBuka = '09:00'; 
        $jamTutup = '17:00';
        
        $isTutup = ($hariIni == 7 || $jamSekarang < $jamBuka || $jamSekarang > $jamTutup);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $order->update(['fulfillment_status' => 'delivered']);

        if ($isTutup) {
            return back()->with('success', 'Pesanan diterima! Akan diproses admin pada jam operasional (09:00 - 17:00).');
        }

        return back()->with('success', 'Terima kasih! Pesanan telah berhasil dikonfirmasi.');
    }

    public function success($id) {
        $order = Order::findOrFail($id);
        return view('user.orderSuccess', compact('order'));
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