<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * PEROMBAKAN ALUR PEMBAYARAN: Midtrans (payment gateway) dihapus total dan
     * diganti Transfer Bank Manual (upload bukti + verifikasi Admin). Kolom
     * `snap_token` hanya berguna untuk membuka ulang sesi popup Midtrans —
     * tidak relevan lagi sama sekali di alur baru, jadi dihapus.
     *
     * CATATAN: kolom `payment_proof` (sudah ada sejak migrasi
     * add_payment_proof_to_orders_table) dan `stock_reserved_at` TETAP
     * DIPERTAHANKAN — keduanya dipakai ulang oleh alur baru (lihat
     * OrderController@uploadProof & Admin\OrderController@approvePayment).
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('payment_channel');
        });
    }
};
