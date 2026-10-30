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
        Schema::create('area_proses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('area_id');
            $table->dateTime('tanggal');
            $table->string('jam');
            $table->enum('kebersihan_ruangan', ['OK', 'Tidak OK']);
            $table->enum('kebersihan_karyawan', ['OK', 'Tidak OK']);
            $table->enum('pemeriksaan_suhu_ruang', ['OK', 'Tidak OK']);
            $table->enum('ketidaksesuaian', ['OK', 'Tidak OK']);
            $table->enum('tindakan_koreksi', ['OK', 'Tidak OK']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('input_area')->onDelete('cascade');
            
            // Index for better performance
            $table->index(['user_id', 'id_plan', 'area_id']);
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_proses');
    }
};
