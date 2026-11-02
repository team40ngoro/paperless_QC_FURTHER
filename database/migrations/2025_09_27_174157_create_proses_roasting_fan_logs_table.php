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
        Schema::create('proses_roasting_fan_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('proses_roasting_fan_id');
            $table->uuid('proses_roasting_fan_uuid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('field_yang_diubah'); // Array of field names that were changed
            $table->json('nilai_lama'); // Array of old values
            $table->json('nilai_baru'); // Array of new values
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('proses_roasting_fan_id')->references('id')->on('proses_roasting_fan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index('proses_roasting_fan_uuid');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proses_roasting_fan_logs');
    }
};
