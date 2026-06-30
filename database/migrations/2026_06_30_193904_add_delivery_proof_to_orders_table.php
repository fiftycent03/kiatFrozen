<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom Proof of Delivery ke tabel orders.
     *
     * delivery_proof : path foto bukti pengiriman yang diupload kurir.
     * delivered_at   : timestamp saat kurir mengkonfirmasi barang sudah tiba.
     * Keduanya nullable karena hanya diisi setelah kurir selesai mengantar.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Path relatif foto (mis. delivery_proofs/abc.jpg) — diakses via asset('storage/...').
            $table->string('delivery_proof')->nullable()->after('payment_proof');
            // Waktu kurir upload bukti; di-cast ke Carbon di Order model untuk ->format().
            $table->timestamp('delivered_at')->nullable()->after('delivery_proof');
        });
    }

    /**
     * Rollback: hapus kedua kolom jika migrasi dibatalkan.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_proof', 'delivered_at']);
        });
    }
};
