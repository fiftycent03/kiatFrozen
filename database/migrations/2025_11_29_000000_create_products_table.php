<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * CATATAN TIMESTAMP:
     * File ini sengaja diberi timestamp 2025_11_29_000000 (satu hari sebelum
     * 2025_11_30_114621_add_category_to_products_table.php) agar tabel `products`
     * sudah dibuat lebih dulu. Laravel menjalankan migrasi berurutan berdasarkan
     * nama file, jadi tabel harus ada sebelum migrasi "alter table" dieksekusi.
     * Inilah perbaikan untuk error: "Base table or view not found: products".
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Kolom dasar produk.
            // `name` WAJIB ada di sini karena migrasi 2025_11_30_114621
            // menambahkan kolom `category` dengan ->after('name').
            $table->string('name');

            // Slug & SKU untuk identitas/URL produk (bagian dari $fillable model Product).
            $table->string('slug')->nullable();
            $table->string('sku_root')->nullable();

            // Harga per kg. WAJIB ada karena migrasi 2026_04_20_091811
            // menambahkan kolom `stock` dengan ->after('price_per_kg').
            $table->decimal('price_per_kg', 12, 2)->default(0);

            // Stok awal dalam gram. Kolom ini nantinya di-rename menjadi
            // `min_pembelian` oleh migrasi 2025_11_30_123400, jadi harus
            // sudah ada sejak tabel dibuat.
            $table->integer('stock_grams')->default(0);

            // Deskripsi & status aktif produk.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        // Hapus tabel products jika ada.
        Schema::dropIfExists('products');
    }
};
