<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * FUNGSI TABEL: Menyimpan Varian Potongan/Gramasi untuk produk bertipe
     * `unit_type = 'kg'` (relasi 1 produk -> banyak varian). Tiap varian punya
     * harga & stok SENDIRI (mis. "500 gram" Rp25.000/stok 10, "1 kg" Rp45.000/
     * stok 6). Produk `unit_type = 'pcs'` TIDAK memakai tabel ini sama sekali —
     * cukup pakai kolom price_per_kg & stock milik produk itu sendiri.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            // Relasi ke produk induk. Varian ikut terhapus bila produknya dihapus (cascade).
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            // Label potongan/gramasi, mis. "500 gram", "1 kg", "Ekor Utuh".
            $table->string('label');

            // Harga & stok KHUSUS varian ini (independen dari products.price_per_kg/stock).
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('stock')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
