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
        if (!Schema::hasTable('pemeriksaan_proses_produksi')) {
            Schema::create('pemeriksaan_proses_produksi', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('id_plan');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('id_area');
                $table->unsignedBigInteger('shift_id');
                $table->datetime('tanggal');
                $table->enum('ketidaksesuaian', ['bahan', 'produk', 'proses']);
                $table->text('uraian_permasalahan');
                $table->text('analisa_penyebab');
                $table->enum('disposisi', ['reject_musnahkan', 'rework', 'rework_perlakuan', 'repack', 'sortir']);
                $table->text('tindakan_koreksi');
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('id_area')->references('id')->on('input_area')->onDelete('cascade');
                $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

                // Indexes untuk performa
                $table->index(['id_plan', 'tanggal']);
                $table->index('user_id');
                $table->index('shift_id');
                $table->index('id_area');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemeriksaan_proses_produksi');
    }
};
