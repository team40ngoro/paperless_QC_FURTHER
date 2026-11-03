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
        Schema::table('suhu_emulsi', function (Blueprint $table) {
            $table->string('berat_bahan')->nullable()->after('kode_produksi_bahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suhu_emulsi', function (Blueprint $table) {
            $table->dropColumn('berat_bahan');
        });
    }
};
