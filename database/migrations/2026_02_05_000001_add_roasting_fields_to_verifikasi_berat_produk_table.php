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
            $table->json('berat_roasting')->nullable()->after('pickup_total_fryer_2');
            $table->decimal('rata_rata_roasting', 8, 2)->nullable()->after('berat_roasting');
            $table->string('pickup_after_breadering_roasting')->nullable()->after('rata_rata_roasting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            $table->dropColumn([
                'berat_roasting',
                'rata_rata_roasting',
                'pickup_after_breadering_roasting',
            ]);
        });
    }
};
