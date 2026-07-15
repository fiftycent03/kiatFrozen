<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * FUNGSI KOLOM: mencatat varian potongan/gramasi mana yang dibeli pada tiap
     * baris pesanan (untuk produk unit_type='kg'). `variant_id` nullable +
     * nullOnDelete mengikuti pola snapshot yang SUDAH ADA di tabel ini
     * (product_id juga nullOnDelete) — jika varian/produk dihapus di kemudian
     * hari, riwayat pesanan TETAP UTUH karena `variant_label_snapshot` menyimpan
     * teks labelnya secara independen dari data varian yang mungkin sudah hilang.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->nullOnDelete();

            // Snapshot label varian saat dibeli, mis. "500 gram". Null untuk produk Pcs.
            $table->string('variant_label_snapshot')->nullable()->after('name_snapshot');
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
            $table->dropColumn('variant_label_snapshot');
        });
    }
};
