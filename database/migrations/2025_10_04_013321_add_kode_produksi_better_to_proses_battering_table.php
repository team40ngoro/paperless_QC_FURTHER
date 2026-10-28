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
        Schema::table('proses_battering', function (Blueprint $table) {
            $table->string('kode_produksi_better')->nullable()->after('id_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses_battering', function (Blueprint $table) {
            $table->dropColumn('kode_produksi_better');
        });
    }
};
