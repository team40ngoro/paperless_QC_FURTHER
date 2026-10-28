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
        Schema::table('ketidaksesuaian_plastik', function (Blueprint $table) {
            $table->string('kode_form', 50)->nullable()->after('dokumentasi_penyimpangan_plastik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ketidaksesuaian_plastik', function (Blueprint $table) {
            $table->dropColumn('kode_form');
        });
    }
};
