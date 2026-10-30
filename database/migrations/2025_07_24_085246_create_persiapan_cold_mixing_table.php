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
        Schema::create('persiapan_cold_mixing', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_suhu_adonan');
            $table->string('rework')->nullable();
            $table->string('hasil_pemeriksaan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('id_plan')->references('id')->on('plan');
            $table->foreign('id_produk')->references('id')->on('jenis_produk');
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
        Schema::dropIfExists('persiapan_cold_mixing');
    }
};
