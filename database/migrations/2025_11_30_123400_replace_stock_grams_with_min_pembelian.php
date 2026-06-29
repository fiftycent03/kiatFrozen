<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {

            // rename kolom
            $table->renameColumn('stock_grams', 'min_pembelian');

            // tambah satuan
            $table->string('satuan')->default('kg')->after('min_pembelian');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {

            $table->renameColumn('min_pembelian', 'stock_grams');
            $table->dropColumn('satuan');
        });
    }
};


