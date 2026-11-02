<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            if (!Schema::hasColumn('bahan_baku_tumbling', 'id_bahan_nonforming')) {
                $table->unsignedBigInteger('id_bahan_nonforming')->nullable()->after('id_produk');
                $table->foreign('id_bahan_nonforming')->references('id')->on('bahan_rm_non_forming')->onDelete('set null');
            }
            if (!Schema::hasColumn('bahan_baku_tumbling', 'manual_bahan_data')) {
                $table->json('manual_bahan_data')->nullable()->after('hasil_pencampuran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            if (Schema::hasColumn('bahan_baku_tumbling', 'manual_bahan_data')) {
                $table->dropColumn('manual_bahan_data');
            }
            if (Schema::hasColumn('bahan_baku_tumbling', 'id_bahan_nonforming')) {
                $table->dropForeign(['id_bahan_nonforming']);
                $table->dropColumn('id_bahan_nonforming');
            }
        });
    }
};