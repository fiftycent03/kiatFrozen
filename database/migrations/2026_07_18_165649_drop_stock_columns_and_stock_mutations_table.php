<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * Field stok dihapus atas permintaan — sistem tidak lagi melacak stok per
     * produk/varian. Ketersediaan beli kini murni ditentukan oleh toggle
     * "Status Produk" (kolom `is_active`). Tabel `stock_mutations` (riwayat
     * mutasi stok) ikut dihapus karena sudah tidak relevan tanpa kolom stock.
     */
    public function up(): void
    {
        Schema::dropIfExists('stock_mutations');

        if (Schema::hasColumn('products', 'stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }

        if (Schema::hasColumn('product_variants', 'stock')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('price_per_kg');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('price');
        });

        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->string('type');
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }
};
