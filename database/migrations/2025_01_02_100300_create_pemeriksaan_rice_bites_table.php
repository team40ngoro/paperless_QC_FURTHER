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
        Schema::create('pemeriksaan_rice_bites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Foreign keys - populated from controller based on logged-in user
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('id_produk');
            
            // Basic information fields
            $table->datetime('tanggal'); // datetime type as requested
            $table->string('batch');
            $table->string('no_cooking_cycle');
            
            // Dynamic array fields for bahan baku (nama, berat, suhu, kondisi)
            $table->json('bahan_baku');
            
            // Dynamic array fields for premix (nama, berat, kondisi)
            $table->json('premix');
            
            // Parameter proses fields
            $table->string('parameter_nitrogen')->nullable();
            $table->string('jumlah_inject_nitrogen')->nullable();
            $table->string('rpm_cooking_cattle')->nullable();
            
            // Cold mixing field
            $table->string('cold_mixing')->nullable();
            
            // Dynamic temperature arrays
            $table->json('suhu_aktual_adonan')->nullable(); // Up to 3 temperature points
            $table->json('suhu_adonan_pencampuran')->nullable(); // Up to 6 temperature points
            
            // Auto-calculated average and result
            $table->decimal('rata_rata_suhu', 8, 2)->nullable();
            $table->enum('hasil_pencampuran', ['OK', 'Tidak OK'])->nullable();
            
            // Notes
            $table->text('catatan')->nullable();
            
            // Verification status fields
            $table->string('diverifikasi_qc')->default('Belum diverifikasi oleh QC');
            $table->integer('diverifikasi_qc_status')->default(0);
            $table->string('diketahui_produksi')->default('Belum diketahui oleh produksi');
            $table->integer('diketahui_produksi_status')->default(0);
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['id_plan', 'tanggal']);
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_rice_bites');
    }
};
