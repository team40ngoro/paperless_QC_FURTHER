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
        Schema::table('input_roasting', function (Blueprint $table) {
            $table->string('std_suhu_sebelum')->nullable()->after('berat_produk');
            $table->string('aktual_suhu_sesudah')->nullable()->after('std_suhu_sebelum');
            $table->dropColumn('waktu_pemasakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('input_roasting', function (Blueprint $table) {
            $table->dropColumn('std_suhu_sebelum');
            $table->dropColumn('aktual_suhu_sesudah');
            $table->time('waktu_pemasakan')->nullable();
        });
    }
};
