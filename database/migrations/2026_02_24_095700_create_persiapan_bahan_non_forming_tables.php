<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('persiapan_bahan_non_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_no_formula_non_forming');
            $table->unsignedBigInteger('shift_id');
            $table->dateTime('tanggal');
            $table->time('jam');
            $table->unsignedBigInteger('id_suhu_adonan')->nullable();
            $table->string('kode_produksi')->nullable();
            $table->string('kode_produksi_emulsi')->nullable();
            $table->json('kode_produksi_emulsi_oil')->nullable();
            $table->time('waktu_mulai_mixing')->nullable();
            $table->time('waktu_selesai_mixing')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('rework')->nullable();
            $table->text('catatan')->nullable();
            $table->string('kode_form')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_no_formula_non_forming')->references('id')->on('no_formula_non_forming')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_suhu_adonan')->references('id')->on('suhu_adonan')->onDelete('set null');
        });

        Schema::create('persiapan_bahan_non_forming_detail', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_persiapan_bahan_non_forming');
            $table->unsignedBigInteger('id_bahan_non_forming');
            $table->string('suhu')->nullable();
            $table->string('kode_produksi_bahan')->nullable();
            $table->timestamps();

            $table->foreign('id_persiapan_bahan_non_forming', 'pb_nf_det_persiapan_fk')
                ->references('id')->on('persiapan_bahan_non_forming')->onDelete('cascade');
            $table->foreign('id_bahan_non_forming', 'pb_nf_det_bahan_fk')
                ->references('id')->on('bahan_rm_non_forming')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('persiapan_bahan_non_forming_detail');
        Schema::dropIfExists('persiapan_bahan_non_forming');
    }
};
