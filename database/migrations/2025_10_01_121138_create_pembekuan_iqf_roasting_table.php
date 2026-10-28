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
        Schema::create('pembekuan_iqf_roasting', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('user_id');
            $table->datetime('tanggal');
            $table->string('suhu_ruang_iqf', 255);
            $table->string('holding_time', 255);
            $table->string('hasil_proses_roasting_uuid', 36)->nullable();
            $table->string('proses_roasting_fan_uuid', 36)->nullable();
            $table->string('frayer_uuid', 36)->nullable();
            $table->string('breader_uuid', 36)->nullable();
            $table->string('battering_uuid', 36)->nullable();
            $table->string('predust_uuid', 36)->nullable();
            $table->string('penggorengan_uuid', 36)->nullable();
            $table->timestamps();
            
            // Foreign key constraints removed to avoid constraint errors
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembekuan_iqf_roasting');
    }
};
