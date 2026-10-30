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
        Schema::create('barang_mudah_pecah_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('barang_mudah_pecah_id');
            $table->uuid('barang_mudah_pecah_uuid');
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
            $table->foreign('barang_mudah_pecah_id', 'brg_mdh_pch_logs_brg_mdh_pch_id_fk')->references('id')->on('barang_mudah_pecah')->onDelete('cascade');
            $table->foreign('user_id', 'brg_mdh_pch_logs_user_id_fk')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('barang_mudah_pecah_id', 'brg_mdh_pch_logs_brg_mdh_pch_id_idx');
            $table->index('user_id', 'brg_mdh_pch_logs_user_id_idx');
            $table->index('created_at', 'brg_mdh_pch_logs_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_mudah_pecah_logs');
    }
};
