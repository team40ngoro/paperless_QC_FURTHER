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
        Schema::create('pengemasan_plastik', function (Blueprint $table) {
            $table->id();
             $table->uuid('uuid')->unique();
            // $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('proses_penimbangan');
            $table->string('proses_sealing');
            $table->string('identitas_produk');
            $table->string('nomor_md');
            $table->string('kode_kemasan_plastik');
            $table->string('kekuatan_seal');
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
        Schema::dropIfExists('pengemasan_plastik');
    }
};
