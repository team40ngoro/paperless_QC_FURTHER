<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_proses_tumbling', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan')->nullable();
            $table->unsignedBigInteger('bahan_baku_tumbling_id')->nullable();
            $table->uuid('bahan_baku_tumbling_uuid')->nullable();
            $table->unsignedBigInteger('proses_aging_id')->nullable();
            $table->uuid('proses_aging_uuid')->nullable();

            $table->string('verifikasi_bahan_baku', 20)->nullable();
            $table->string('verifikasi_hasil_pencampuran', 20)->nullable();
            $table->string('suhu_bahan_baku', 100)->nullable();
            $table->string('kondisi_bahan_baku', 20)->nullable();

            $table->string('verifikasi_proses_tumbling', 20)->nullable();
            $table->string('suhu_hasil_tumbling', 100)->nullable();
            $table->string('sensori_hasil_tumbling', 20)->nullable();

            $table->json('aging_data')->nullable();
            $table->string('waktu_selesai_aging', 100)->nullable();

            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('bahan_baku_tumbling_id');
            $table->index('proses_aging_id');
            $table->index('verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_proses_tumbling');
    }
};
