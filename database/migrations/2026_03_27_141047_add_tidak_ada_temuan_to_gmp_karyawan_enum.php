<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan 'tidak_ada_temuan' ke kolom ENUM. 
        // Kita juga menyertakan 'sesuai' agar data lama tetap aman sebelum di-migrasi.
        DB::statement("ALTER TABLE gmp_karyawan MODIFY COLUMN temuan_ketidaksesuaian ENUM('sesuai', 'perlengkapan', 'kuku', 'perhiasan', 'luka', 'tidak_ada_temuan')");

        // Migrasi data lama dari 'sesuai' ke 'tidak_ada_temuan' agar konsisten dengan label di model
        DB::table('gmp_karyawan')->where('temuan_ketidaksesuaian', 'sesuai')->update(['temuan_ketidaksesuaian' => 'tidak_ada_temuan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan data 'tidak_ada_temuan' ke 'sesuai' jika migrasi di-rollback
        DB::table('gmp_karyawan')->where('temuan_ketidaksesuaian', 'tidak_ada_temuan')->update(['temuan_ketidaksesuaian' => 'sesuai']);

        // Kembalikan ENUM ke kondisi sebelumnya
        DB::statement("ALTER TABLE gmp_karyawan MODIFY COLUMN temuan_ketidaksesuaian ENUM('sesuai', 'perlengkapan', 'kuku', 'perhiasan', 'luka')");
    }
};
