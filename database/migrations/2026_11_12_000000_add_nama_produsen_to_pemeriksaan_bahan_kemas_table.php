<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_bahan_kemas', function (Blueprint $table) {
            $table->string('nama_produsen')->nullable()->after('shift_id');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_bahan_kemas', function (Blueprint $table) {
            $table->dropColumn('nama_produsen');
        });
    }
};
