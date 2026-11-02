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
        Schema::create('frayer_4', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('id_suhu_frayer');
            $table->unsignedBigInteger('id_waktu_penggorengan');
            $table->string('aktual_penggorengan');
            $table->string('tpm_minyak');
            $table->datetime('tanggal');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_suhu_frayer')->references('id')->on('suhu_frayer_1')->onDelete('cascade');
            $table->foreign('id_waktu_penggorengan')->references('id')->on('waktu_penggorengan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('frayer_4');
    }
};
