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
        Schema::create('pemasakan_nasi_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('pemasakan_nasi_id');
            $table->uuid('pemasakan_nasi_uuid');
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
            $table->foreign('pemasakan_nasi_id')->references('id')->on('pemasakan_nasi')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('pemasakan_nasi_id', 'pmskn_nsi_logs_pmskn_nsi_id_idx');
            $table->index('user_id', 'pmskn_nsi_logs_user_id_idx');
            $table->index('created_at', 'pmskn_nsi_logs_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasakan_nasi_logs');
    }
};
