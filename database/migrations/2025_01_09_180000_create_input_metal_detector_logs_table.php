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
        Schema::create('input_metal_detector_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Foreign keys
            $table->unsignedBigInteger('input_metal_detector_id');
            $table->uuid('input_metal_detector_uuid');
            $table->unsignedBigInteger('user_id');
            
            // User info
            $table->string('user_name');
            $table->string('user_role');
            
            // Log details
            $table->string('aksi'); // create, update, delete
            $table->json('field_yang_diubah')->nullable(); // Array field yang diubah
            $table->json('nilai_lama')->nullable(); // Array nilai sebelum diubah
            $table->json('nilai_baru')->nullable(); // Array nilai setelah diubah
            
            // Additional info
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('input_metal_detector_id')->references('id')->on('input_metal_detector')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('input_metal_detector_id');
            $table->index('input_metal_detector_uuid');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('input_metal_detector_logs');
    }
};
