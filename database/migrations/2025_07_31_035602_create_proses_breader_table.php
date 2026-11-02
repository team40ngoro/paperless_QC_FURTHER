<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProsesBreaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proses_breader', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_jenis_breader');
            $table->unsignedBigInteger('id_shift');
            $table->string('kode_produksi');
            $table->string('hasil_breader');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('restrict');
            $table->foreign('id_jenis_breader')->references('id')->on('jenis_breader')->onDelete('restrict');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proses_breader');
    }
};
