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
        Schema::create('kontrol_sanitasi_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('kontrol_sanitasi_id');
            $table->uuid('kontrol_sanitasi_uuid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('aksi');
            $table->json('field_yang_diubah')->nullable();
            $table->json('nilai_lama')->nullable();
            $table->json('nilai_baru')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('kontrol_sanitasi_id', 'kontrol_sanitasi_logs_kontrol_sanitasi_id_fk')->references('id')->on('kontrol_sanitasi')->onDelete('cascade');
            $table->foreign('user_id', 'kontrol_sanitasi_logs_user_id_fk')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('kontrol_sanitasi_id', 'kontrol_sanitasi_logs_kontrol_sanitasi_id_idx');
            $table->index('user_id', 'kontrol_sanitasi_logs_user_id_idx');
            $table->index('created_at', 'kontrol_sanitasi_logs_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrol_sanitasi_logs');
    }
};
