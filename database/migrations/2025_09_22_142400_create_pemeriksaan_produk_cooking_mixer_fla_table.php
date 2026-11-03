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
        Schema::create('pemeriksaan_produk_cooking_mixer_fla', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->datetime('tanggal');
            $table->unsignedBigInteger('id_frm_fla');
            $table->unsignedBigInteger('id_stp_frm_fla');
            $table->unsignedBigInteger('id_nama_formula_fla');
            $table->string('kode_produksi');
            $table->string('berat');
            $table->string('waktu_start');
            $table->string('waktu_stop');
            $table->string('sensori_kondisi');
            $table->boolean('status_gas')->default(0);
            $table->string('lama_proses');
            $table->string('speed');
            $table->string('temp_std_1');
            $table->string('temp_std_2');
            $table->string('temp_std_3');
            $table->enum('organo_warna', ['OK', 'Tidak OK']);
            $table->enum('organo_aroma', ['OK', 'Tidak OK']);
            $table->enum('organo_tekstur', ['OK', 'Tidak OK']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_frm_fla')->references('id')->on('bahan_formula_fla')->onDelete('cascade');
            $table->foreign('id_stp_frm_fla')->references('id')->on('nomor_step_formula_fla')->onDelete('cascade');
            $table->foreign('id_nama_formula_fla')->references('id')->on('nama-formula-fla')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_produk_cooking_mixer_fla');
    }
};
