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
        Schema::table('data_defect', function (Blueprint $table) {
            // Kita drop kolom-kolom lama yang flat/tidak terstruktur
            $table->dropColumn([
                'jenis_defect',
                'spec_defect',
                'kategori_ketidaksesuaian',
                'jenis_ketidaksesuaian',
                'spesifikasi'
            ]);

            // Tambahkan kolom detail_defect yang menampung nested JSON
            $table->json('detail_defect')->nullable()->after('nama_produsen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_defect', function (Blueprint $table) {
            $table->dropColumn('detail_defect');
            $table->string('jenis_defect')->nullable();
            $table->string('spec_defect')->nullable();
            $table->json('kategori_ketidaksesuaian')->nullable();
            $table->json('jenis_ketidaksesuaian')->nullable();
            $table->json('spesifikasi')->nullable();
        });
    }
};
