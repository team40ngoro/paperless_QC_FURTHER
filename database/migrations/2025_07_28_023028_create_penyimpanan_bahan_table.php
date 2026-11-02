<?php
namespace App\Models;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('penyimpanan_bahan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');
            $table->string('pemeriksaan_kondisi')->nullable();
            $table->string('pemeriksaan_kebersihan')->nullable();
            $table->string('kebersihan_ruang')->nullable();
            $table->string('suhu_ruang')->nullable();
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penyimpanan_bahan');
    }
};