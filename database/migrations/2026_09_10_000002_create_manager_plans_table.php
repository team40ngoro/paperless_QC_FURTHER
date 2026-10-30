<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plan')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'plan_id']); // Cegah duplikasi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_plans');
    }
};
