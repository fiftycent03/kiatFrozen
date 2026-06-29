<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function salesReport()
    {
        $sales = Order::where('payment_status', 'paid')
                      ->latest('paid_at')
                      ->paginate(15);

        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalTransactions = Order::where('payment_status', 'paid')->count();

        return view('admin.sales.index', compact('sales', 'totalRevenue', 'totalTransactions'));
    }

    public function quickProcess(Order $order)
    {
        // 1. Logika Dasar: Setiap admin klik Petir, status kirim jadi 'shipped'
        // dan kolom shipping_date diisi (sesuai field database Anda)
        $updateData = [
            'fulfillment_status' => 'shipped',
            'shipping_date' => now(), 
        ];

        // 2. Logika Cabang: Cek apakah pembayaran via COD atau Transfer
        // Jika BUKAN COD (Berarti Transfer), maka otomatis lunas
        if (strtolower($order->payment_channel) !== 'cod') {
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at'] = now();
        } 
        // Jika COD, biarkan status pembayaran tetap 'pending' 
        // karena uang baru diterima kurir nanti saat sampai.

        $order->update($updateData);

        return back()->with('success', "Order {$order->code} diproses: STATUS DIKIRIM.");
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,rejected',
            'fulfillment_status' => 'required|in:pending,processing,shipped,delivered',
            'shipping_tracking_number' => 'nullable|string'
        ]);

        $data = [
            'payment_status' => $request->payment_status,
            'fulfillment_status' => $request->fulfillment_status,
            'shipping_tracking_number' => $request->shipping_tracking_number,
        ];

        if ($request->payment_status == 'paid' && is_null($order->paid_at)) {
            $data['paid_at'] = now();
        }

        $order->update($data);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}