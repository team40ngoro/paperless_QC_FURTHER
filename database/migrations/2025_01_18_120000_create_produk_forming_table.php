<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('user_id');
            $table->datetime('tanggal');
            
            // A. Bahan Baku
            $table->json('bahan_baku')->nullable();
            
            // B. Bahan Penunjang
            $table->json('bahan_penunjang')->nullable();
            
            // C. Kemasan
            $table->integer('kemasan_plastik')->nullable();
            $table->integer('kemasan_karton')->nullable();
            $table->integer('labelisasi_plastik')->nullable();
            $table->integer('labelisasi_karton')->nullable();
            
            // D. Mesin Dan Peralatan
            $table->integer('autogrind')->nullable();
            $table->integer('bowlcutter')->nullable();
            $table->integer('ayakan_seasoning')->nullable();
            $table->integer('unimix')->nullable();
            $table->integer('revoformer')->nullable();
            $table->integer('better_mixer')->nullable();
            $table->integer('wet_coater')->nullable();
            $table->integer('breader')->nullable();
            $table->integer('frayer_1')->nullable();
            $table->integer('frayer_2')->nullable();
            $table->integer('iqf_jbt')->nullable();
            $table->integer('keranjang')->nullable();
            $table->integer('timbangan')->nullable();
            $table->integer('mhw')->nullable();
            $table->integer('foot_sealer')->nullable();
            $table->integer('metal_detector')->nullable();
            $table->integer('rotary_table')->nullable();
            $table->integer('carton_sealer')->nullable();
            $table->integer('meatcar')->nullable();
            $table->integer('check_weigher_bag')->nullable();
            $table->integer('check_weigher_box')->nullable();
            
            // Penilaian untuk setiap field
            $table->json('penilaian')->nullable();
            
            $table->text('tindakan_koreksi')->nullable();
            $table->string('verifikasi')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['id_produk', 'id_plan', 'tanggal']);
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_forming');
    }
};
