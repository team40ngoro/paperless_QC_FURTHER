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
        Schema::create('bahan_formula_fla', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_nama_formula_fla');
            $table->unsignedBigInteger('id_nomor_step_formula_fla');
            $table->text('bahan_formula_fla'); // JSON array for dynamic forms
            $table->text('berat_formula_fla'); // JSON array for dynamic forms
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_nama_formula_fla')->references('id')->on('nama-formula-fla')->onDelete('cascade');
            $table->foreign('id_nomor_step_formula_fla')->references('id')->on('nomor_step_formula_fla')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_formula_fla');
    }
};
