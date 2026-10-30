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
        Schema::create('label_start_up_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_start_up_id')->constrained('label_start_ups')->onDelete('cascade');
            $table->unsignedBigInteger('id_produk');
            $table->time('jam');
            $table->string('foto_kode_produksi');
            $table->enum('hasil_pengecekan', ['OK', 'Tidak OK']);
            $table->string('nama_operator');
            $table->longText('ttd_operator');
            $table->string('nama_qc');
            $table->longText('ttd_qc');
            $table->text('tindakan_koreksi')->nullable();
            $table->timestamps();

            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('label_start_up_details');
    }
};
