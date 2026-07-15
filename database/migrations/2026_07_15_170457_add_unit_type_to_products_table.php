<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * FUNGSI KOLOM: `unit_type` adalah SAKLAR BISNIS baru yang menentukan apakah
     * sebuah produk memakai harga & stok TUNGGAL (Pcs) atau WAJIB punya beberapa
     * Varian Potongan/Gramasi dengan harga & stok masing-masing (Kg).
     *
     * CATATAN: kolom `satuan` (lama) TETAP ADA dan tidak diubah — dipakai di
     * banyak halaman lama (katalog, keranjang, checkout) sebagai teks satuan
     * tampilan. Di form Admin, satu dropdown "Tipe Penjualan / Satuan" yang sama
     * kini mengisi KEDUA kolom sekaligus (nilainya identik: 'pcs' atau 'kg'),
     * sehingga tidak ada UI ganda yang membingungkan Admin — lihat
     * ProductController@store/update untuk detailnya.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('unit_type', ['pcs', 'kg'])->default('pcs')->after('satuan');
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('unit_type');
        });
    }
};
