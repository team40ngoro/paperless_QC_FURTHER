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
        Schema::create('pembuatan_predust', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_jenis_predust');
            $table->datetime('tanggal');
            $table->string('kondisi_predust');
            $table->string('hasil_pencetakan');
            $table->string('kode_produksi');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('id_jenis_predust')->references('id')->on('jenis_predust')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['id_plan', 'id_produk']);
            $table->index('user_id');
            $table->index('id_jenis_predust');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembuatan_predust');
    }
};
