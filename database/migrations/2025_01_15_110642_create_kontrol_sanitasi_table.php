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
        Schema::create('kontrol_sanitasi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->datetime('tanggal');
            $table->integer('suhu_air');
            $table->string('kadar_klorin_food_basin');
            $table->string('kadar_klorin_hand_basin');
            $table->string('hasil_verifikasi');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

            // Indexes
            $table->index('uuid');
            $table->index('id_plan');
            $table->index('user_id');
            $table->index('shift_id');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kontrol_sanitasi');
    }
};
