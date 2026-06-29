<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // 1. Menambahkan kolom stock ke tabel products jika belum ada
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'stock')) {
                    // Meletakkan kolom stock setelah price_per_kg agar rapi di database
                    $table->integer('stock')->default(0)->after('price_per_kg');
                }
            });
        }

        // 2. Membuat tabel stock_mutations untuk riwayat stok
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel products
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            
            // Jumlah perubahan (positif untuk masuk, negatif untuk keluar)
            $table->integer('quantity'); 
            
            // Tipe mutasi: 'in' (masuk/restock) atau 'out' (keluar/penjualan)
            $table->string('type'); 
            
            // Referensi tambahan (misal: "Order ORD-123" atau "Input Admin")
            $table->string('reference')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        // Menghapus tabel mutasi terlebih dahulu karena ada foreign key
        Schema::dropIfExists('stock_mutations');

        // Menghapus kolom stock di tabel products
        if (Schema::hasColumn('products', 'stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
    }
};