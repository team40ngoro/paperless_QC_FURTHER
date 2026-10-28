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
        Schema::create('pemeriksaan_bahan_kemas_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('pemeriksaan_bahan_kemas_id');
            $table->uuid('pemeriksaan_bahan_kemas_uuid');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();

            $table->string('aksi')->nullable();
            $table->json('field_yang_diubah')->nullable();
            $table->json('nilai_lama')->nullable();
            $table->json('nilai_baru')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('pemeriksaan_bahan_kemas_id')
                ->references('id')
                ->on('pemeriksaan_bahan_kemas')
                ->onDelete('cascade');

            $table->index('pemeriksaan_bahan_kemas_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemeriksaan_bahan_kemas_logs');
    }
};
