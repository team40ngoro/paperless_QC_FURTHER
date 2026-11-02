<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('aktual_suhu_adonan', function (Blueprint $table) {
            if (!Schema::hasColumn('aktual_suhu_adonan', 'owner_type')) {
                $table->string('owner_type')->nullable()->after('uuid');
            }
            if (!Schema::hasColumn('aktual_suhu_adonan', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('owner_type');
            }

            // Compatibility columns (some environments may not have them despite code references)
            if (!Schema::hasColumn('aktual_suhu_adonan', 'id_persiapan_bahan_forming')) {
                $table->unsignedBigInteger('id_persiapan_bahan_forming')->nullable()->after('owner_id');
            }
            if (!Schema::hasColumn('aktual_suhu_adonan', 'id_persiapan_bahan_non_forming')) {
                $table->unsignedBigInteger('id_persiapan_bahan_non_forming')->nullable()->after('id_persiapan_bahan_forming');
            }
            if (!Schema::hasColumn('aktual_suhu_adonan', 'id_persiapan_cold_mixing')) {
                $table->unsignedBigInteger('id_persiapan_cold_mixing')->nullable()->after('id_persiapan_bahan_non_forming');
            }
        });

        // Backfill owner_* from legacy columns
        if (Schema::hasColumn('aktual_suhu_adonan', 'id_persiapan_bahan_forming')) {
            DB::table('aktual_suhu_adonan')
                ->whereNull('owner_type')
                ->whereNotNull('id_persiapan_bahan_forming')
                ->update([
                    'owner_type' => 'App\\Models\\PersiapanBahanForming',
                    'owner_id' => DB::raw('id_persiapan_bahan_forming'),
                ]);
        }

        if (Schema::hasColumn('aktual_suhu_adonan', 'id_persiapan_bahan_non_forming')) {
            DB::table('aktual_suhu_adonan')
                ->whereNull('owner_type')
                ->whereNotNull('id_persiapan_bahan_non_forming')
                ->update([
                    'owner_type' => 'App\\Models\\PersiapanBahanNonForming',
                    'owner_id' => DB::raw('id_persiapan_bahan_non_forming'),
                ]);
        }

        if (Schema::hasColumn('aktual_suhu_adonan', 'id_persiapan_cold_mixing')) {
            DB::table('aktual_suhu_adonan')
                ->whereNull('owner_type')
                ->whereNotNull('id_persiapan_cold_mixing')
                ->update([
                    'owner_type' => 'App\\Models\\PersiapanColdMixing',
                    'owner_id' => DB::raw('id_persiapan_cold_mixing'),
                ]);
        }

        Schema::table('aktual_suhu_adonan', function (Blueprint $table) {
            $table->index(['owner_type', 'owner_id'], 'aktual_suhu_owner_idx');
        });
    }

    public function down()
    {
        Schema::table('aktual_suhu_adonan', function (Blueprint $table) {
            if (Schema::hasColumn('aktual_suhu_adonan', 'owner_type') && Schema::hasColumn('aktual_suhu_adonan', 'owner_id')) {
                $table->dropIndex('aktual_suhu_owner_idx');
                $table->dropColumn(['owner_type', 'owner_id']);
            }
        });
    }
};
