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
        Schema::create('pemeriksaan_rheon_machine_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('pemeriksaan_rheon_machine_id');
            $table->string('pemeriksaan_rheon_machine_uuid');
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
            $table->index('pemeriksaan_rheon_machine_id', 'idx_rheon_machine_logs_id');
            $table->index('pemeriksaan_rheon_machine_uuid', 'idx_rheon_machine_logs_uuid');
            $table->index('user_id', 'idx_rheon_machine_logs_user');
            $table->index('created_at', 'idx_rheon_machine_logs_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_rheon_machine_logs');
    }
};
