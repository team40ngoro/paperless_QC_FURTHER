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
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('pemeriksaan_benda_asing')) {
            Schema::create('pemeriksaan_benda_asing', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('id_plan');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('shift_id');
                $table->unsignedBigInteger('id_produk');
                $table->datetime('tanggal');
                $table->time('jam');
                $table->string('jenis_kontaminasi');
                $table->string('bukti')->nullable(); // untuk file foto
                $table->string('kode_produksi');
                $table->string('ukuran_kontaminasi');
                $table->string('ditemukan');
                $table->text('analisa_masalah')->nullable();
                $table->text('koreksi')->nullable();
                $table->text('tindak_korektif')->nullable();
                $table->string('diketahui')->nullable();
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
                $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');

                // Indexes untuk performa
                $table->index(['id_plan', 'tanggal']);
                $table->index('user_id');
                $table->index('shift_id');
                $table->index('id_produk');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_benda_asing');
    }
};
