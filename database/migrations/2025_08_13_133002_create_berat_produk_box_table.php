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
        Schema::create('berat_produk_box', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->foreignId('id_data_box')->constrained('data_box')->onDelete('cascade');
            $table->date('tanggal');
            $table->float('berat_aktual_1');
            $table->float('berat_aktual_2');
            $table->float('berat_aktual_3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('berat_produk_box');
    }
};
