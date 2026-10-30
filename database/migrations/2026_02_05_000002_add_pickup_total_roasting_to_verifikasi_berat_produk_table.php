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
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            $table->string('pickup_total_roasting')->nullable()->after('pickup_after_breadering_roasting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            $table->dropColumn('pickup_total_roasting');
        });
    }
};
