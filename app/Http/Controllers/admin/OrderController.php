<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders  = Order::with('courier')->latest()->paginate(10);
        // Daftar kurir aktif untuk dropdown penugasan di form "KIRIM".
        $couriers = User::where('role', 'kurir')->orderBy('name')->get();
        return view('admin.orders.index', compact('orders', 'couriers'));
    }

    public function salesReport()
    {
        $sales = Order::where('payment_status', 'paid')
                      ->latest('paid_at')
                      ->paginate(15);

        $totalRevenue     = Order::where('payment_status', 'paid')->sum('total');
        $totalTransactions = Order::where('payment_status', 'paid')->count();

        return view('admin.sales.index', compact('sales', 'totalRevenue', 'totalTransactions'));
    }

    public function quickProcess(Request $request, Order $order)
    {
        $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $updateData = [
            'fulfillment_status' => 'shipped',
            'shipping_date'      => now(),
            // Tugaskan kurir yang dipilih admin ke pesanan ini.
            'courier_id'         => $request->courier_id,
        ];

        // Jika bukan COD, langsung tandai lunas saat dikirim.
        if (strtolower($order->payment_channel) !== 'cod') {
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at']        = now();
        }

        $order->update($updateData);

        $courier = User::find($request->courier_id);
        return back()->with('success', "Order {$order->code} dikirim via kurir: {$courier->name}.");
    }

    public function downloadProof($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->delivery_proof || !Storage::disk('public')->exists($order->delivery_proof)) {
            return back()->with('error', 'File bukti pengiriman tidak ditemukan.');
        }

        $filename = 'bukti-pengiriman-' . $order->code . '.jpg';
        return Storage::disk('public')->download($order->delivery_proof, $filename);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'payment_status'           => 'required|in:pending,paid,rejected',
            'fulfillment_status'       => 'required|in:pending,processing,shipped,delivered,received',
            'shipping_tracking_number' => 'nullable|string',
        ]);

        $data = [
            'payment_status'           => $request->payment_status,
            'fulfillment_status'       => $request->fulfillment_status,
            'shipping_tracking_number' => $request->shipping_tracking_number,
        ];

        if ($request->payment_status == 'paid' && is_null($order->paid_at)) {
            $data['paid_at'] = now();
        }

        $order->update($data);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
