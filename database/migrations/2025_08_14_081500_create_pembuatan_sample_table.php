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
        Schema::create('pembuatan_sample', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->string('kode_produksi');
            $table->date('tanggal');
            $table->date('tanggal_expired');
            $table->integer('jumlah');
            $table->decimal('berat', 8, 2);
            $table->string('jenis_sample');
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
        Schema::dropIfExists('pembuatan_sample');
    }
};
