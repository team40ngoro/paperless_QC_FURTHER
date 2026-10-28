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
        Schema::table('data_defect', function (Blueprint $table) {
            // Tambah kolom baru
            $table->string('nama_produsen')->nullable()->after('id_plan');
            $table->json('kategori_ketidaksesuaian')->nullable()->after('nama_produsen');
            $table->json('jenis_ketidaksesuaian')->nullable()->after('kategori_ketidaksesuaian');
            $table->json('spesifikasi')->nullable()->after('jenis_ketidaksesuaian');
        });

        // Migrasi data lama: pindah jenis_defect -> kategori_ketidaksesuaian, spec_defect -> spesifikasi
        DB::table('data_defect')->get()->each(function ($row) {
            $kategori = $row->jenis_defect ? [$row->jenis_defect] : [];
            $spesifikasi = $row->spec_defect ? [$row->spec_defect] : [];
            DB::table('data_defect')->where('id', $row->id)->update([
                'kategori_ketidaksesuaian' => json_encode($kategori),
                'jenis_ketidaksesuaian'    => json_encode([]),
                'spesifikasi'              => json_encode($spesifikasi),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_defect', function (Blueprint $table) {
            $table->dropColumn(['nama_produsen', 'kategori_ketidaksesuaian', 'jenis_ketidaksesuaian', 'spesifikasi']);
        });
    }
};
