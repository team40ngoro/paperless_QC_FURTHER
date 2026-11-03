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
        Schema::create('pemeriksaan_produk_cooking_mixer_fla_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('pemeriksaan_produk_cooking_mixer_fla_id');
            $table->string('pemeriksaan_produk_cooking_mixer_fla_uuid');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('aksi');
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Indexes dengan nama yang lebih pendek
            $table->index('pemeriksaan_produk_cooking_mixer_fla_id', 'idx_cooking_mixer_fla_logs_id');
            $table->index('pemeriksaan_produk_cooking_mixer_fla_uuid', 'idx_cooking_mixer_fla_logs_uuid');
            $table->index('user_id', 'idx_cooking_mixer_fla_logs_user');
            $table->index('created_at', 'idx_cooking_mixer_fla_logs_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_produk_cooking_mixer_fla_logs');
    }
};
