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
        Schema::create('release_produk_finish_goods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->date('tanggal');
            $table->time('jam');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('user_id');
            
            // Suhu
            $table->decimal('suhu_iqf', 8, 2);
            $table->decimal('suhu_produk', 8, 2);
            
            // Proses
            $table->integer('lama_pembekuan');
            
            // Labelisasi (Foto)
            $table->string('foto_kemasan_sekunder')->nullable();
            $table->string('foto_kemasan_primer')->nullable();
            
            // Fisik
            $table->text('kondisi_kemasan_primer');
            $table->decimal('berat_packing_sekunder', 10, 2);
            $table->integer('jumlah_box');
            
            // Kesimpulan
            $table->string('status'); // OK / NG
            $table->text('keterangan')->nullable();
            $table->text('tindakan_koreksi')->nullable();
            
            $table->timestamps();

            // Foreign keys (optional but good practice)
            // $table->foreign('id_plan')->references('id')->on('plans');
            // $table->foreign('id_shift')->references('id')->on('data_shifts');
            // $table->foreign('id_produk')->references('id')->on('jenis_produk');
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('release_produk_finish_goods');
    }
};
