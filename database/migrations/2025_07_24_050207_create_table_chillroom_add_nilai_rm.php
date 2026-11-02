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
        Schema::create('penerimaan_chillroom', function (Blueprint $table) {
            $table->id();
              $table->uuid('uuid')->unique();
            $table->string('nama_rm');
            $table->string('kode_produksi');
             $table->string('shift_id');
            $table->string('berat');
            $table->string('suhu');
            $table->string('sensori');
            $table->date('tanggal'); 
            $table->string('kemasan');
            $table->string('keterangan');
            $table->string('standar_berat');
            $table->string('jumlah_rm');
            $table->string('nilai_jumlah_rm');
            $table->string('status_rm');
            $table->string('catatan_rm');
             $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

      
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penerimaan_chillroom');
    }
};
