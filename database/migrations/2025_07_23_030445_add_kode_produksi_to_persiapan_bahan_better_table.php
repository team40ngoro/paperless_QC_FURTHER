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
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->string('kode_produksi_produk')->nullable();
            $table->string('kode_produksi_better')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->dropColumn(['kode_produksi_produk', 'kode_produksi_better']);
        });
    }
};
