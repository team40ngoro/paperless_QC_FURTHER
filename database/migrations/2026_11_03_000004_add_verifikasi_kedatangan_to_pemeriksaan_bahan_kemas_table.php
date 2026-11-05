<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_bahan_kemas', function (Blueprint $table) {
            $table->string('verifikasi_kedatangan')->nullable()->after('keterangan');
            $table->unsignedBigInteger('verifikasi_kedatangan_by')->nullable()->after('verifikasi_kedatangan');
            $table->timestamp('verifikasi_kedatangan_at')->nullable()->after('verifikasi_kedatangan_by');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_bahan_kemas', function (Blueprint $table) {
            $table->dropColumn([
                'verifikasi_kedatangan',
                'verifikasi_kedatangan_by',
                'verifikasi_kedatangan_at',
            ]);
        });
    }
};
