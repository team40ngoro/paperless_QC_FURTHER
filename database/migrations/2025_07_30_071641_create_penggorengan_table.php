<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenggorenganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penggorengan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->string('kode_produksi');
            $table->string('no_of_strokes');
            $table->string('waktu_pemasakan');
            $table->date('tanggal');
            $table->string('hasil_pencetakan');
            $table->timestamps();

            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('restrict');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('restrict');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penggorengan');
    }
};
