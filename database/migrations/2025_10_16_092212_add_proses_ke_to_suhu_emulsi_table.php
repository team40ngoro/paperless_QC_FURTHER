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
            $table->integer('proses_ke')->default(1)->after('suhu')->comment('Nomor urut proses emulsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suhu_emulsi', function (Blueprint $table) {
            $table->dropColumn('proses_ke');
        });
    }
};