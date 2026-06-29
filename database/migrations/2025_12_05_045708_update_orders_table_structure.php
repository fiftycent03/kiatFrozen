<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Hapus QRIS
        $table->dropColumn(['qris_string', 'qris_expired_at']);

        // Modifikasi zone_id
        $table->unsignedBigInteger('zone_id')->nullable()->change();

        // Tambah kolom baru
        $table->string('province')->nullable()->after('customer_address');
        $table->string('shipping_service')->default('standard')->after('shipping_fee');
        $table->string('shipping_tracking_number')->nullable()->after('fulfillment_status');
    });
}

public function down()
{
    // Kebalikan dari up (opsional, untuk rollback)
    Schema::table('orders', function (Blueprint $table) {
        $table->longText('qris_string')->nullable();
        $table->dateTime('qris_expired_at')->nullable();
        $table->unsignedBigInteger('zone_id')->nullable(false)->change();
        $table->dropColumn(['province', 'shipping_service', 'shipping_tracking_number']);
    });
}
};
