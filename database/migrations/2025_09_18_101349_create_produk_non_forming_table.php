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
        Schema::create('produk_non_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('user_id');
            $table->datetime('tanggal');
            
            // A. Bahan Baku (JSON untuk multiple entries)
            $table->json('bahan_baku')->nullable();
            
            // B. Bahan Penunjang (JSON untuk multiple entries)
            $table->json('bahan_penunjang')->nullable();
            
            // C. Kemasan
            $table->integer('kemasan_plastik')->nullable();
            $table->integer('kemasan_karton')->nullable();
            $table->integer('labelisasi_plastik')->nullable();
            $table->integer('labelisasi_karton')->nullable();
            
            // D. Mesin Dan Peralatan
            $table->integer('tumbler')->nullable();
            $table->integer('frayer')->nullable();
            $table->integer('hicook')->nullable();
            $table->integer('iqf_advance_1')->nullable();
            $table->integer('iqf_advance_2')->nullable();
            $table->integer('keranjang')->nullable();
            $table->integer('palet')->nullable();
            $table->integer('meatcar')->nullable();
            $table->integer('timbangan')->nullable();
            $table->integer('mhw')->nullable();
            $table->integer('foot_sealer')->nullable();
            $table->integer('metal_detector')->nullable();
            $table->integer('check_weigher_bag')->nullable();
            $table->integer('check_weigher_box')->nullable();
            $table->integer('karton_sealer')->nullable();
            
            // Penilaian untuk setiap field
            $table->json('penilaian')->nullable();
            
            // Tindakan Koreksi dan Verifikasi
            $table->text('tindakan_koreksi')->nullable();
            $table->string('verifikasi')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['id_produk', 'id_plan', 'id_shift']);
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk_non_forming');
    }
};
