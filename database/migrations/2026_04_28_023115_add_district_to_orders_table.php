<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Menambahkan kolom district setelah kolom city
        $table->string('district')->nullable()->after('city');
        // Menambahkan kolom weight untuk audit jika nanti ada komplain ongkir
        $table->decimal('total_weight', 8, 2)->default(0)->after('subtotal');
    });
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
