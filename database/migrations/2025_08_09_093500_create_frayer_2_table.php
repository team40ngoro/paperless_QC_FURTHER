<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('frayer_2', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->date('tanggal');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_suhu_frayer_1');
            $table->unsignedBigInteger('id_waktu_penggorengan');
            $table->string('aktual_penggorengan');
            $table->string('tpm_minyak');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('id_suhu_frayer_1')->references('id')->on('suhu_frayer_1')->onDelete('cascade');
            $table->foreign('id_waktu_penggorengan')->references('id')->on('waktu_penggorengan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frayer_2');
    }
};
