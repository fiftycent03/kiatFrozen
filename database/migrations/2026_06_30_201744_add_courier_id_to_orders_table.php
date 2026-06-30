<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kurir yang ditugaskan admin untuk mengantar pesanan ini.
            // nullable: pesanan baru belum punya kurir sampai admin assign via quickProcess.
            // nullOnDelete: jika akun kurir dihapus, kolom ini jadi null (bukan error FK).
            $table->foreignId('courier_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['courier_id']);
            $table->dropColumn('courier_id');
        });
    }
};
