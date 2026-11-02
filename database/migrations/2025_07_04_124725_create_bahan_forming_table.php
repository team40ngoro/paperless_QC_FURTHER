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
        Schema::create('bahan_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_formula');
            $table->string('nama_rm');
            $table->string('berat_rm');
            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
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
        Schema::dropIfExists('bahan_forming');
    }
};
