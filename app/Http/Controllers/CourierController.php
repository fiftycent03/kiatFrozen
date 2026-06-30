<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourierController extends Controller
{
    /**
     * Halaman utama kurir: daftar pesanan yang siap diantarkan (status 'shipped')
     * dan daftar pesanan yang sudah diantarkan hari ini (status 'delivered').
     */
    public function index()
    {
        $courierId = Auth::id();

        // Hanya tampilkan pesanan yang ditugaskan ke kurir yang sedang login.
        $pendingOrders = Order::where('fulfillment_status', 'shipped')
            ->where('courier_id', $courierId)
            ->latest()
            ->get();

        // Pesanan yang sudah diantarkan kurir ini hari ini — untuk referensi.
        $deliveredToday = Order::where('fulfillment_status', 'delivered')
            ->where('courier_id', $courierId)
            ->whereDate('delivered_at', today())
            ->latest('delivered_at')
            ->get();

        return view('courier.dashboard', compact('pendingOrders', 'deliveredToday'));
    }

    /**
     * Proses upload bukti pengiriman dari kurir.
     *
     * Alur:
     * 1. Validasi: wajib ada file foto (image, maks 4MB).
     * 2. Simpan foto ke storage/public/delivery_proofs.
     * 3. Isi kolom delivery_proof, delivered_at, dan ubah fulfillment_status ke 'delivered'.
     *
     * Setelah ini, halaman orderDetail customer akan menampilkan foto bukti
     * dan mengaktifkan tombol "Konfirmasi Pesanan Diterima".
     */
    public function updateDelivery(Request $request, $id)
    {
        $request->validate([
            // Foto wajib ada; batasi 4MB dan hanya format gambar agar aman di server.
            'delivery_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $order = Order::findOrFail($id);

        // Tolak jika pesanan bukan dalam status 'shipped' — cegah upload ganda atau salah status.
        if ($order->fulfillment_status !== 'shipped') {
            return back()->with('error', 'Pesanan ini tidak dalam status pengiriman.');
        }

        // Pastikan kurir hanya bisa upload bukti untuk pesanan yang ditugaskan kepadanya.
        if ($order->courier_id !== Auth::id()) {
            return back()->with('error', 'Pesanan ini bukan tanggung jawab Anda.');
        }

        // Simpan foto ke storage/public/delivery_proofs dan dapatkan path-nya.
        $path = $request->file('delivery_proof')->store('delivery_proofs', 'public');

        // Update tiga kolom sekaligus dalam satu query:
        // delivery_proof  = path file foto untuk ditampilkan ke customer.
        // delivered_at    = waktu sekarang sebagai timestamp pengantaran.
        // fulfillment_status = 'delivered' agar customer melihat bukti & tombol konfirmasi.
        $order->update([
            'delivery_proof'     => $path,
            'delivered_at'       => now(),
            'fulfillment_status' => 'delivered',
        ]);

        return back()->with('success', "Bukti pengiriman pesanan #{$order->code} berhasil diunggah.");
    }
}
