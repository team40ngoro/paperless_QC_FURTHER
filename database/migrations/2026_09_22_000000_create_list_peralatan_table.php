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
        Schema::create('list_peralatan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_alat');
            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_area')->references('id')->on('input_area')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['id_plan', 'id_area']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_peralatan');
    }
};
