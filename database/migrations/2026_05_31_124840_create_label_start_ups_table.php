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
        Schema::create('label_start_ups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('label_start_ups');
    }
};
