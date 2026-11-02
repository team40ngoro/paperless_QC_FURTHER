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
        Schema::table('suhu_frayer_2', function (Blueprint $table) {
            $table->string('waktu_penggorengan_2')->nullable()->after('suhu_frayer_2')->comment('Waktu penggorengan Frayer 2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suhu_frayer_2', function (Blueprint $table) {
          $table->dropColumn('waktu_penggorengan_2');
        });
    }
};
