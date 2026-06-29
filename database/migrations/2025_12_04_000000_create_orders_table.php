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
     * Sengaja diberi timestamp 2025_12_04_000000 (satu hari sebelum
     * 2025_12_05_045708_update_orders_table_structure.php) agar tabel `orders`
     * sudah terbentuk lebih dulu. Laravel mengeksekusi migrasi berurutan
     * berdasarkan nama file, sehingga tabel dasar harus ada sebelum migrasi
     * "alter table" (drop/modify/add kolom) dijalankan. Ini memperbaiki error:
     * "Base table or view not found: orders".
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Kode unik pesanan (mis. nomor invoice).
            $table->string('code')->unique();

            // Status pembayaran & pemenuhan pesanan.
            // `fulfillment_status` WAJIB ada di sini karena migrasi
            // 2025_12_05_045708 memakai ->after('fulfillment_status').
            $table->string('payment_status')->default('pending');
            $table->string('fulfillment_status')->default('pending');

            // zone_id dibuat TIDAK nullable di sini, karena migrasi
            // 2025_12_05_045708 secara eksplisit mengubahnya menjadi nullable
            // (->nullable()->change()). Tidak diberi foreign key karena
            // tabel `zones` tidak ada di project ini.
            $table->unsignedBigInteger('zone_id');

            // Ringkasan biaya pesanan.
            // `subtotal` & `shipping_fee` WAJIB ada karena dipakai sebagai
            // acuan ->after() oleh migrasi-migrasi berikutnya.
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Data pengiriman & pelanggan.
            $table->date('shipping_date')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            // `customer_address` WAJIB ada karena migrasi 2025_12_05_045708
            // memakai ->after('customer_address') untuk kolom `province`.
            $table->text('customer_address');
            // `city` WAJIB ada karena migrasi 2026_04_28_023115 memakai
            // ->after('city') untuk kolom `district`.
            $table->string('city');

            $table->text('notes')->nullable();
            $table->string('payment_channel')->nullable();
            $table->dateTime('paid_at')->nullable();

            // Kolom QRIS lama yang nantinya DIHAPUS oleh migrasi
            // 2025_12_05_045708 (dropColumn qris_string, qris_expired_at).
            // Harus dibuat di sini agar operasi drop tersebut valid.
            $table->longText('qris_string')->nullable();
            $table->dateTime('qris_expired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        // Hapus tabel orders jika ada.
        Schema::dropIfExists('orders');
    }
};
