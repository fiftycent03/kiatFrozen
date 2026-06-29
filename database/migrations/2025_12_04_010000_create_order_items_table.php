<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * FUNGSI TABEL: Menyimpan rincian item/baris produk di dalam sebuah pesanan
     * (relasi 1 order -> banyak order_items). Dipakai oleh Model App\Models\OrderItem
     * dan relasi items() di Order, serta diisi di OrderController saat checkout.
     *
     * CATATAN TIMESTAMP:
     * Diberi timestamp 2025_12_04_010000 — tepat SETELAH 2025_12_04_000000_create_orders_table —
     * karena tabel ini punya foreign key ke `orders` (dan ke `products`), sehingga kedua
     * tabel induk tersebut wajib sudah ada lebih dulu agar migrasi tidak error.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Relasi ke pesanan induk. Item ikut terhapus bila ordernya dihapus (cascade).
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');

            // Relasi ke produk. Dibuat nullable + nullOnDelete: jika produk dihapus,
            // baris pesanan TIDAK ikut hilang (riwayat penjualan tetap utuh) —
            // datanya tetap terbaca lewat kolom snapshot di bawah.
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete();

            // Jumlah satuan (kg) yang dibeli untuk item ini.
            $table->integer('qty');

            // Subtotal harga item ini (qty * harga).
            $table->decimal('subtotal', 12, 2)->default(0);

            // Snapshot data produk SAAT pemesanan, agar riwayat tidak berubah
            // walau data produk asli nanti diedit/dihapus.
            $table->string('name_snapshot');                       // nama produk saat dibeli
            $table->decimal('price_per_kg_snapshot', 12, 2);       // harga/kg saat dibeli

            // Total berat item dalam gram (untuk perhitungan ongkir/audit).
            $table->integer('grams')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
