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
        Schema::create('pembekuan_iqf_penggorengan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_shift')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->datetime('tanggal');
            $table->string('suhu_ruang_iqf');
            $table->string('holding_time');
            
            // UUID relationships
            $table->string('hasil_penggorengan_uuid')->nullable();
            $table->string('frayer_uuid')->nullable();
            $table->string('frayer2_uuid')->nullable();
            $table->string('breader_uuid')->nullable();
            $table->string('battering_uuid')->nullable();
            $table->string('predust_uuid')->nullable();
            $table->string('penggorengan_uuid')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembekuan_iqf_penggorengan');
    }
};
