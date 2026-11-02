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
        Schema::create('reboxes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama_produk');
            $table->string('kode_produksi');
            $table->string('best_before');
            $table->string('isi_jumlah');
            $table->string('labelisasi');
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
        Schema::dropIfExists('reboxes');
    }
};
