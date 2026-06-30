<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * Menambahkan kolom `image` ke tabel categories untuk menyimpan path gambar
     * kategori (hasil upload Admin). Dipakai sebagai background card kategori di
     * dashboard user. Nullable karena kategori lama belum punya gambar.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Path gambar kategori, contoh: "categories/seafood.jpg" (disk 'public').
            $table->string('image')->nullable()->after('slug');
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
