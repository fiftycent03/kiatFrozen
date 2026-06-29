<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * FUNGSI TABEL: Master kategori produk (contoh: "Fillet Ikan", "Cumi").
     * Tabel ini direferensikan oleh products.category_id (FK ditambahkan di migrasi
     * 2025_12_02_144914_add_category_id_to_products_table).
     *
     * CATATAN TIMESTAMP:
     * Sebelumnya file ini bertimestamp 2025_12_02_144055 (dibuat SETELAH products).
     * Dipindah ke 2025_11_28_000000 agar tabel MASTER ini terbentuk paling awal —
     * mengikuti hierarki database e-commerce yang benar: users & categories dulu,
     * baru products, lalu orders. Tetap berada sebelum migrasi yang menambah FK
     * category_id ke products, sehingga relasi tidak error.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Nama kategori, contoh: "Fillet Ikan"
            $table->string('slug')->unique();  // Slug unik untuk URL, contoh: "fillet-ikan"
            $table->boolean('is_active')->default(true); // Status tampil/sembunyi kategori
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
