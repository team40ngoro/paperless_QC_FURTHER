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
        Schema::dropIfExists('proses_roasting_fan_2');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('proses_roasting_fan_2', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_suhu_blok')->constrained('suhu_blok')->onDelete('cascade');
            $table->foreignId('id_std_fan')->constrained('std_fan')->onDelete('cascade');
            $table->string('suhu_roasting');
            $table->date('tanggal');
            $table->string('fan_1');
            $table->string('fan_2');
            $table->timestamps();
        });
    }
};
