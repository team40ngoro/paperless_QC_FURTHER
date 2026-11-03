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
        Schema::create('ketidaksesuaian_plastik_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('ketidaksesuaian_plastik_id');
            $table->uuid('ketidaksesuaian_plastik_uuid');
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
            $table->foreign('ketidaksesuaian_plastik_id')->references('id')->on('ketidaksesuaian_plastik')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('ketidaksesuaian_plastik_id', 'ktdkss_plstk_logs_ktdkss_plstk_id_idx');
            $table->index('user_id', 'ktdkss_plstk_logs_user_id_idx');
            $table->index('created_at', 'ktdkss_plstk_logs_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketidaksesuaian_plastik_logs');
    }
};
