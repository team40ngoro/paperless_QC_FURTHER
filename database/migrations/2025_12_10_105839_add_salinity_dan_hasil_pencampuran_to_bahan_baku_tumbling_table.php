<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            if (!Schema::hasColumn('bahan_baku_tumbling', 'salinity')) {
                $table->string('salinity', 255)->nullable()->after('kondisi_daging');
            }
            if (!Schema::hasColumn('bahan_baku_tumbling', 'hasil_pencampuran')) {
                $table->string('hasil_pencampuran', 255)->nullable()->after('salinity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            if (Schema::hasColumn('bahan_baku_tumbling', 'salinity')) {
                $table->dropColumn('salinity');
            }
            if (Schema::hasColumn('bahan_baku_tumbling', 'hasil_pencampuran')) {
                $table->dropColumn('hasil_pencampuran');
            }
        });
    }
};