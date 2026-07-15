<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * PERBAIKAN BUG: "SQLSTATE[23000]: Duplicate entry '1' for key products.PRIMARY"
     *
     * Kolom `id` pada tabel `products` SUDAH BENAR memakai `$table->id()` (BigInt
     * AUTO_INCREMENT) sejak awal — bukan itu penyebabnya. Masalahnya ada di DATA:
     * counter AUTO_INCREMENT internal MySQL untuk tabel ini tertinggal di belakang
     * MAX(id) yang sebenarnya ada di tabel (mis. akibat insert dengan id eksplisit
     * saat testing/seeding, atau tabel sempat dibangun ulang oleh migrasi rename
     * kolom sebelumnya). Akibatnya, INSERT baru dari ProductController@store
     * (yang TIDAK menyertakan kolom id sama sekali, sesuai perilaku Eloquent
     * normal) mencoba memakai id yang SUDAH DIPAKAI baris lain -> bentrok.
     *
     * FIX: hitung ulang MAX(id) yang sebenarnya, lalu paksa counter AUTO_INCREMENT
     * ke (MAX(id) + 1). Ini TIDAK mengubah data apa pun, hanya metadata counter —
     * aman dijalankan berkali-kali dan tidak butuh migrate:fresh (data existing
     * tetap utuh).
     */
    public function up(): void
    {
        // COALESCE(MAX(id), 0) + 1 -> aman walau tabel products kosong (hasil jadi 1).
        $nextId = DB::table('products')->max('id');
        $nextId = ($nextId ?? 0) + 1;

        // ALTER TABLE ... AUTO_INCREMENT tidak bisa memakai subquery langsung di
        // MySQL, karena itu nilainya dihitung dulu di PHP (baris di atas) baru
        // disisipkan ke statement ALTER TABLE di bawah ini.
        DB::statement("ALTER TABLE products AUTO_INCREMENT = {$nextId}");
    }

    /**
     * Batalkan migrasi (rollback).
     *
     * Tidak ada "nilai lama" yang berarti untuk dikembalikan — counter yang lama
     * justru SALAH (itulah bug yang sedang diperbaiki). down() sengaja dibiarkan
     * kosong agar rollback tidak menyebabkan bug ini muncul lagi.
     */
    public function down(): void
    {
        //
    }
};
