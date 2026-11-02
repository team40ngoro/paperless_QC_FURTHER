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
        Schema::table('proses_twahing_detail', function (Blueprint $table) {
            $table->decimal('suhu_rm', 8, 2)->nullable()->after('waktu_pemeriksaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses_twahing_detail', function (Blueprint $table) {
            $table->dropColumn('suhu_rm');
        });
    }
};
