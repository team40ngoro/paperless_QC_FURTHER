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
         Schema::table('area_proses', function (Blueprint $table) {
            $table->string('kondisi_barang')->nullable()->after('kebersihan_ruangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('area_proses', function (Blueprint $table) {
            $table->dropColumn('kondisi_barang');
        });
    }
};
