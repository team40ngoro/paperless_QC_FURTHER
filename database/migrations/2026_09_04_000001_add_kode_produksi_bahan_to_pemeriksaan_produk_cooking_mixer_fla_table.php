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
        Schema::table('pemeriksaan_produk_cooking_mixer_fla', function (Blueprint $table) {
            $table->text('kode_produksi_bahan')->nullable()->after('id_nama_formula_fla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_produk_cooking_mixer_fla', function (Blueprint $table) {
            $table->dropColumn('kode_produksi_bahan');
        });
    }
};
