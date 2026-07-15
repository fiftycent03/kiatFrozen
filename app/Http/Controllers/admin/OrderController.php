<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
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

        // PERBAIKAN ALUR: dulu order non-COD otomatis ditandai 'paid' begitu
        // diklik "KIRIM" — sisa peninggalan integrasi Midtrans (pembayaran
        // dianggap pasti sudah lunas via gateway). Sekarang pembayaran Transfer
        // Bank Manual WAJIB diverifikasi Admin lewat tombol "ACC Pembayaran"
        // (lihat approvePayment() di bawah, yang JUGA mengurangi stok) — jadi
        // quickProcess() di sini TIDAK BOLEH lagi ikut mengubah payment_status,
        // supaya status 'paid' tidak pernah muncul tanpa stok benar-benar
        // dikurangi lebih dulu.
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

    /**
     * Tombol "ACC Pembayaran": Admin mengonfirmasi bukti transfer yang di-upload
     * user itu SAH. Menggantikan callback onSuccess/onPending Midtrans yang sudah
     * dihapus — di sinilah satu-satunya tempat payment_status boleh menjadi 'paid'
     * DAN stok produk/varian benar-benar dikurangi.
     */
    public function approvePayment(Order $order)
    {
        // Hanya order yang sedang menunggu verifikasi yang boleh di-ACC — mencegah
        // klik ganda/keliru meng-ACC order yang belum ada bukti sama sekali atau
        // yang sudah lunas/ditolak sebelumnya.
        if ($order->payment_status !== 'awaiting_verification') {
            return back()->with('error', 'Order ini tidak sedang menunggu verifikasi pembayaran.');
        }

        // Kurangi stok HANYA SEKALI per order (dijaga stock_reserved_at) — kolom
        // yang sama yang dulu dipakai penanda idempoten di callback Midtrans,
        // kini bertugas persis sama untuk mencegah klik ACC dobel.
        if (!$order->stock_reserved_at) {
            $order->load('items');
            foreach ($order->items as $item) {
                // LOGIKA IF/ELSE PCS vs KG (pengurangan stok): baris dengan
                // variant_id (unit_type='kg') mengurangi stok VARIAN itu sendiri,
                // bukan stok utama produk induk — tiap potongan/gramasi punya
                // stoknya sendiri di tabel product_variants.
                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)->decrement('stock', $item->qty);
                } else {
                    Product::where('id', $item->product_id)->decrement('stock', $item->qty);
                }
            }
            $order->stock_reserved_at = now();
        }

        $order->payment_status = 'paid';
        $order->paid_at        = now();
        $order->save();

        return back()->with('success', "Pembayaran order {$order->code} berhasil di-ACC. Stok telah dikurangi.");
    }

    /**
     * Tombol "Tolak Bukti": bukti transfer yang di-upload user dianggap TIDAK
     * SAH (mis. salinan lama, nominal tidak cocok, dsb). Status dikembalikan ke
     * 'rejected' agar user tahu harus upload ulang — stok TIDAK disentuh sama
     * sekali karena approvePayment() belum pernah menguranginya untuk order ini.
     */
    public function rejectPayment(Order $order)
    {
        if ($order->payment_status !== 'awaiting_verification') {
            return back()->with('error', 'Order ini tidak sedang menunggu verifikasi pembayaran.');
        }

        $order->update(['payment_status' => 'rejected']);

        return back()->with('success', "Bukti transfer order {$order->code} ditolak. User perlu upload ulang.");
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'payment_status'           => 'required|in:pending,awaiting_verification,paid,rejected',
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
