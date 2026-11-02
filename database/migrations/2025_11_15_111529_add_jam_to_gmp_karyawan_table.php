<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            $table->time('jam')->nullable()->after('tanggal')->comment('Waktu pelaksanaan proses gmp karyawan');
        });
    }

    public function down(): void
    {
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            $table->dropColumn('jam');
        });
    }
};