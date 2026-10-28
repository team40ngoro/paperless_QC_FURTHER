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
        // Drop bahan_baku_roasting_uuid from hasil_proses_roasting table
        if (Schema::hasColumn('hasil_proses_roasting', 'bahan_baku_roasting_uuid')) {
            Schema::table('hasil_proses_roasting', function (Blueprint $table) {
                $table->dropColumn('bahan_baku_roasting_uuid');
            });
        }

        // Drop bahan_baku_roasting_uuid from pembekuan_iqf_roasting table
        if (Schema::hasColumn('pembekuan_iqf_roasting', 'bahan_baku_roasting_uuid')) {
            Schema::table('pembekuan_iqf_roasting', function (Blueprint $table) {
                $table->dropColumn('bahan_baku_roasting_uuid');
            });
        }

        // Drop bahan_baku_roasting_uuid from proses_roasting_fan table
        if (Schema::hasColumn('proses_roasting_fan', 'bahan_baku_roasting_uuid')) {
            Schema::table('proses_roasting_fan', function (Blueprint $table) {
                $table->dropColumn('bahan_baku_roasting_uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back bahan_baku_roasting_uuid to hasil_proses_roasting table
        if (!Schema::hasColumn('hasil_proses_roasting', 'bahan_baku_roasting_uuid')) {
            Schema::table('hasil_proses_roasting', function (Blueprint $table) {
                $table->uuid('bahan_baku_roasting_uuid')->nullable();
            });
        }

        // Add back bahan_baku_roasting_uuid to pembekuan_iqf_roasting table
        if (!Schema::hasColumn('pembekuan_iqf_roasting', 'bahan_baku_roasting_uuid')) {
            Schema::table('pembekuan_iqf_roasting', function (Blueprint $table) {
                $table->uuid('bahan_baku_roasting_uuid')->nullable();
            });
        }

        // Add back bahan_baku_roasting_uuid to proses_roasting_fan table
        if (!Schema::hasColumn('proses_roasting_fan', 'bahan_baku_roasting_uuid')) {
            Schema::table('proses_roasting_fan', function (Blueprint $table) {
                $table->uuid('bahan_baku_roasting_uuid')->nullable();
            });
        }
    }
};
