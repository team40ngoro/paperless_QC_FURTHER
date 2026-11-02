<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembuatanEmulsiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembuatan_emulsi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Foreign keys
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('nama_emulsi_id');
            $table->unsignedBigInteger('total_pemakaian_id');
            $table->unsignedBigInteger('nomor_emulsi_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');

            // Data utama
            $table->string('kode_produksi_emulsi');
            $table->string('suhu')->nullable();
            $table->string('hasil_emulsi')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('nama_emulsi_id')->references('id')->on('jenis_emulsi')->onDelete('cascade');
            $table->foreign('total_pemakaian_id')->references('id')->on('total_pemakaian_emulsi')->onDelete('cascade');
            $table->foreign('nomor_emulsi_id')->references('id')->on('nomor_emulsi')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembuatan_emulsi');
    }
}
