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
        Schema::create('data_tumbling', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('user_id');
            $table->time('drum_on');
            $table->time('drum_off');
            $table->decimal('drum_speed', 8, 2);
            $table->time('total_waktu');
            $table->decimal('tekanan_vakum', 8, 2);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_tumbling');
    }
};
