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
        Schema::table('pemeriksaan_rice_bites', function (Blueprint $table) {
            // Drop the old verification columns
            $table->dropColumn([
                'diverifikasi_qc',
                'diverifikasi_qc_status',
                'diketahui_produksi',
                'diketahui_produksi_status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_rice_bites', function (Blueprint $table) {
            // Restore the old verification columns
            $table->string('diverifikasi_qc')->nullable()->after('catatan');
            $table->integer('diverifikasi_qc_status')->default(0)->after('diverifikasi_qc');
            $table->string('diketahui_produksi')->nullable()->after('diverifikasi_qc_status');
            $table->integer('diketahui_produksi_status')->default(0)->after('diketahui_produksi');
        });
    }
};
