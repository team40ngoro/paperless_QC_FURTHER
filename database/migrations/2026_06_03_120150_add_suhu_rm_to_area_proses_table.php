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
            $table->string('suhu_rm')->nullable()->after('kondisi_barang');
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
            $table->dropColumn('suhu_rm');
        });
    }
};
