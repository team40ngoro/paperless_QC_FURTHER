<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStdSalinitasViskositasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('std_salinitas_viskositas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_better');
            $table->string('std_viskositas');
            $table->string('std_salinitas');
            $table->string('std_suhu_akhir');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('id_plan')->references('id')->on('plan');
            $table->foreign('id_produk')->references('id')->on('jenis_produk');
            $table->foreign('id_better')->references('id')->on('jenis_better');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('std_salinitas_viskositas');
    }
};
