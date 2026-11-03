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
        Schema::create('pengemasan_produk', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->date('tanggal');
            $table->date('tanggal_expired');
            $table->string('kode_produksi');
            $table->string('std_suhu_produk_iqf')->default('-18°C');
            $table->float('aktual_suhu_produk');
            $table->time('waktu_awal_packing');
            $table->time('waktu_selesai_packing');
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
        Schema::dropIfExists('pengemasan_produk');
    }
};
