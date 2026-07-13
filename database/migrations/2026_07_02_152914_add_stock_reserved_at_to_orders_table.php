<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Penanda kapan stok produk untuk order ini benar-benar dikurangi.
            // Diisi SEKALI saja (saat onSuccess ATAU onPending Midtrans pertama kali
            // terpicu) agar percobaan bayar ulang (retry via tombol "Lanjutkan
            // Pembayaran") tidak mengurangi stok dua kali untuk order yang sama.
            $table->timestamp('stock_reserved_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_reserved_at');
        });
    }
};
