<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proses_twahing', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_shift');
            $table->date('tanggal');

            $table->time('waktu_thawing_awal')->nullable();
            $table->time('waktu_thawing_akhir')->nullable();
            $table->enum('kondisi_kemasan_rm', ['utuh', 'sobek'])->nullable();
            $table->decimal('total_waktu_thawing_jam', 8, 2)->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('restrict');

            $table->index(['id_plan', 'tanggal']);
            $table->index('user_id');
            $table->index('id_shift');
        });

        Schema::create('proses_twahing_detail', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('proses_twahing_id');
            $table->unsignedBigInteger('id_rm');

            $table->string('kode_produksi')->nullable();
            $table->string('kondisi_ruang')->nullable();
            $table->time('waktu_pemeriksaan')->nullable();

            $table->decimal('suhu_ruang', 8, 2)->nullable();
            $table->decimal('suhu_air_thawing', 8, 2)->nullable();
            $table->decimal('suhu_produk', 8, 2)->nullable();

            $table->string('kondisi_produk')->nullable();

            $table->timestamps();

            $table->foreign('proses_twahing_id')->references('id')->on('proses_twahing')->onDelete('cascade');
            $table->foreign('id_rm')->references('id')->on('data_rm')->onDelete('restrict');

            $table->index('proses_twahing_id');
            $table->index('id_rm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proses_twahing_detail');
        Schema::dropIfExists('proses_twahing');
    }
};
