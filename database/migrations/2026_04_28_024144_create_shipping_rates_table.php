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
    Schema::create('shipping_rates', function (Blueprint $table) {
        $table->id();
        $table->string('province_name');
        $table->string('city_name');
        $table->string('district_name');
        $table->decimal('cost', 10, 2); // Tarif yang berbeda tiap wilayah
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
