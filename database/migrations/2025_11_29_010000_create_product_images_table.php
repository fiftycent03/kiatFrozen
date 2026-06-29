<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * FUNGSI TABEL: Menyimpan galeri foto untuk tiap produk (relasi 1 produk -> banyak gambar).
     * Dipakai oleh Model App\Models\ProductImage serta relasi images()/primaryImage() di Product.
     *
     * CATATAN TIMESTAMP:
     * Diberi timestamp 2025_11_29_010000 — tepat SETELAH 2025_11_29_000000_create_products_table
     * (selisih beberapa menit) — karena tabel ini memiliki foreign key ke `products`,
     * sehingga tabel `products` wajib sudah terbentuk lebih dulu.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            // Relasi ke produk. Jika produk dihapus, semua gambarnya ikut terhapus (cascade).
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            // Lokasi/path file gambar di storage (mis. "products/abc.jpg").
            $table->string('path');

            // Penanda gambar utama (thumbnail) produk. Dipakai oleh primaryImage().
            $table->boolean('is_primary')->default(false);

            // Model ProductImage memakai $timestamps = false, jadi TIDAK ada kolom timestamps.
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
