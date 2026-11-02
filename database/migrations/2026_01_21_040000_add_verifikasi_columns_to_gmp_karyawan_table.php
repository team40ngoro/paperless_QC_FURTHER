<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('gmp_karyawan', 'verifikasi')) {
                $table->enum('verifikasi', ['ok', 'tidak_ok'])->nullable()->after('tindakan_koreksi');
            }

            if (!Schema::hasColumn('gmp_karyawan', 'koreksi_lanjutan')) {
                $table->enum('koreksi_lanjutan', ['ok', 'tidak_ok'])->nullable()->after('verifikasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('gmp_karyawan', 'koreksi_lanjutan')) {
                $table->dropColumn('koreksi_lanjutan');
            }

            if (Schema::hasColumn('gmp_karyawan', 'verifikasi')) {
                $table->dropColumn('verifikasi');
            }
        });
    }
};
