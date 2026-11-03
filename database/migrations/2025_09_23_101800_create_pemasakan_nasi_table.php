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
        Schema::create('pemasakan_nasi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('id_produk');
            $table->datetime('tanggal');
            $table->string('kode_produksi');
            $table->string('waktu_start');
            $table->string('waktu_stop');
            $table->string('proses');
            $table->string('waktu');
            $table->json('jenis_bahan');
            $table->json('jumlah');
            $table->string('sensori_kondisi');
            $table->boolean('status_cooking')->default(0);
            $table->string('lama_proses');
            $table->string('temp_std_1');
            $table->string('temp_std_2');
            $table->string('temp_std_3');
            $table->string('organo_warna');
            $table->string('organo_aroma');
            $table->string('organo_rasa');
            $table->string('organo_tekstur');
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemasakan_nasi');
    }
};
