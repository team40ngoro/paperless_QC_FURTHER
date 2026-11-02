<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Periksa dan tambahkan field relasi ke tabel proses_marinade jika belum ada
        if (!Schema::hasColumn('proses_marinade', 'bahan_baku_tumbling_uuid')) {
            Schema::table('proses_marinade', function (Blueprint $table) {
                $table->uuid('bahan_baku_tumbling_uuid')->nullable()->after('uuid');
                $table->index('bahan_baku_tumbling_uuid');
            });
        }

        if (!Schema::hasColumn('proses_marinade', 'bahan_baku_tumbling_id')) {
            Schema::table('proses_marinade', function (Blueprint $table) {
                $table->unsignedBigInteger('bahan_baku_tumbling_id')->nullable()->after('bahan_baku_tumbling_uuid');
                $table->index('bahan_baku_tumbling_id');
            });
        }

        // Periksa dan tambahkan field relasi ke tabel proses_tumbling jika belum ada
        if (!Schema::hasColumn('proses_tumbling', 'bahan_baku_tumbling_uuid')) {
            Schema::table('proses_tumbling', function (Blueprint $table) {
                $table->uuid('bahan_baku_tumbling_uuid')->nullable()->after('uuid');
                $table->index('bahan_baku_tumbling_uuid');
            });
        }

        if (!Schema::hasColumn('proses_tumbling', 'bahan_baku_tumbling_id')) {
            Schema::table('proses_tumbling', function (Blueprint $table) {
                $table->unsignedBigInteger('bahan_baku_tumbling_id')->nullable()->after('bahan_baku_tumbling_uuid');
                $table->index('bahan_baku_tumbling_id');
            });
        }

        if (!Schema::hasColumn('proses_tumbling', 'proses_marinade_uuid')) {
            Schema::table('proses_tumbling', function (Blueprint $table) {
                $table->uuid('proses_marinade_uuid')->nullable()->after('bahan_baku_tumbling_id');
                $table->index('proses_marinade_uuid');
            });
        }

        if (!Schema::hasColumn('proses_tumbling', 'proses_marinade_id')) {
            Schema::table('proses_tumbling', function (Blueprint $table) {
                $table->unsignedBigInteger('proses_marinade_id')->nullable()->after('proses_marinade_uuid');
                $table->index('proses_marinade_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_marinade', function (Blueprint $table) {
            if (Schema::hasColumn('proses_marinade', 'bahan_baku_tumbling_uuid')) {
                $table->dropIndex(['bahan_baku_tumbling_uuid']);
                $table->dropColumn('bahan_baku_tumbling_uuid');
            }
            if (Schema::hasColumn('proses_marinade', 'bahan_baku_tumbling_id')) {
                $table->dropIndex(['bahan_baku_tumbling_id']);
                $table->dropColumn('bahan_baku_tumbling_id');
            }
        });

        Schema::table('proses_tumbling', function (Blueprint $table) {
            if (Schema::hasColumn('proses_tumbling', 'bahan_baku_tumbling_uuid')) {
                $table->dropIndex(['bahan_baku_tumbling_uuid']);
                $table->dropColumn('bahan_baku_tumbling_uuid');
            }
            if (Schema::hasColumn('proses_tumbling', 'bahan_baku_tumbling_id')) {
                $table->dropIndex(['bahan_baku_tumbling_id']);
                $table->dropColumn('bahan_baku_tumbling_id');
            }
            if (Schema::hasColumn('proses_tumbling', 'proses_marinade_uuid')) {
                $table->dropIndex(['proses_marinade_uuid']);
                $table->dropColumn('proses_marinade_uuid');
            }
            if (Schema::hasColumn('proses_tumbling', 'proses_marinade_id')) {
                $table->dropIndex(['proses_marinade_id']);
                $table->dropColumn('proses_marinade_id');
            }
        });
    }
};
