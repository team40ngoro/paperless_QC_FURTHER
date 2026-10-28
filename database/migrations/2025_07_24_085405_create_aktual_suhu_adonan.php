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
        Schema::create('aktual_suhu_adonan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_persiapan_cold_mixing');
            $table->unsignedBigInteger('id_suhu_adonan');
            $table->float('aktual_suhu_1')->nullable();
            $table->float('aktual_suhu_2')->nullable();
            $table->float('aktual_suhu_3')->nullable();
            $table->float('aktual_suhu_4')->nullable();
            $table->float('aktual_suhu_5')->nullable();
            $table->float('total_aktual_suhu')->nullable();
            $table->timestamps();

            $table->foreign('id_persiapan_cold_mixing')->references('id')->on('persiapan_cold_mixing')->onDelete('cascade');
            $table->foreign('id_suhu_adonan')->references('id')->on('suhu_adonan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktual_suhu_adonan');
    }
};
