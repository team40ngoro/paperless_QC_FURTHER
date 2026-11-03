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
        Schema::create('dokumentasi', function (Blueprint $table) {
             $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->string('foto_kode_produksi');
            $table->string('qr_code');
            $table->string('label_polyroll');
            $table->date('tanggal');
          
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
        Schema::dropIfExists('dokumentasi');
    }
};
