<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verif_cip', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->json('payload');
            $table->timestamps();

            $table->index('id_plan');
            $table->index('user_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verif_cip');
    }
};
