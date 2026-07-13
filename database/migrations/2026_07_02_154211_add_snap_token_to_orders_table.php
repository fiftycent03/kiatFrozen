<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Menyimpan Snap Token Midtrans hasil generate saat checkout, supaya
            // tombol "Lanjutkan Pembayaran" di halaman Order Detail bisa membuka
            // ULANG SESI PEMBAYARAN YANG SAMA (bukan bikin transaksi baru tiap kali dibuka).
            $table->string('snap_token')->nullable()->after('payment_channel');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });
    }
};
