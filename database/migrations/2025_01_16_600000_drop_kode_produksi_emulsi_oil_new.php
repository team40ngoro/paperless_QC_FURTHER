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
        Schema::table('persiapan_bahan_forming', function (Blueprint $table) {
            $table->dropColumn('kode_produksi_emulsi_oil');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persiapan_bahan_forming', function (Blueprint $table) {
            $table->json('kode_produksi_emulsi_oil')->nullable()->after('kondisi');
        });
    }
};
