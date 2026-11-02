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
        Schema::table('suhu_frayer_1', function (Blueprint $table) {
            $table->string('waktu_penggorengan_1')->nullable()->after('suhu_frayer')->comment('Waktu penggorengan  Frayer 1');
            $table->string('waktu_penggorengan_3')->nullable()->after('suhu_frayer_3')->comment('Waktu penggorengan Frayer 3 ');
            $table->string('waktu_penggorengan_4')->nullable()->after('suhu_frayer_4')->comment('Waktu penggorengan  Frayer 4 ');
            $table->string('waktu_penggorengan_5')->nullable()->after('suhu_frayer_5')->comment('Waktu penggorengan  Frayer 5 ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suhu_frayer_1', function (Blueprint $table) {
             $table->dropColumn([
                'waktu_penggorengan_1',
                'waktu_penggorengan_3',
                'waktu_penggorengan_4',
                'waktu_penggorengan_5'
            ]);
        });
    }
};
