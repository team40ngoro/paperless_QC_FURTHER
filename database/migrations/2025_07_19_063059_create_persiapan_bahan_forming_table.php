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
        Schema::create('persiapan_bahan_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_formula'); // relasi ke formula
            $table->string('kode_produksi_emulsi')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('kode_produksi_emulsi_oil')->nullable();
            $table->string('rework')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_formula')->references('id')->on('nomor_formula')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persiapan_bahan_forming');
    }
};
