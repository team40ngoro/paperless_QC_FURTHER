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
        Schema::create('data_barang', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_barang');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['id_plan', 'user_id']);
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_barang');
    }
};
