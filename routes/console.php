<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Order::where('fulfillment_status', 'shipped')
        ->where('updated_at', '<=', now()->subDays(4))
        ->update(['fulfillment_status' => 'delivered']);
})->everyMinute();

// Bersihkan file bukti pengiriman yang sudah lebih dari 30 hari.
// File dihapus dari storage agar tidak menumpuk; kolom di DB di-null-kan.
Schedule::call(function () {
    $orders = Order::whereNotNull('delivery_proof')
        ->where('delivered_at', '<=', now()->subDays(30))
        ->get();

    foreach ($orders as $order) {
        Storage::disk('public')->delete($order->delivery_proof);
        $order->update(['delivery_proof' => null]);
    }
})->daily()->name('hapus-bukti-kirim-30-hari');
