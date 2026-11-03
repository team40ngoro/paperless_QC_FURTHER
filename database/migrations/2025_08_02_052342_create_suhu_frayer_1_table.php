<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuhuFrayer1Table extends Migration
{
    public function up()
    {
        Schema::create('suhu_frayer_1', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_plan');
            $table->string('suhu_frayer');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('id_produk')->references('id')->on('jenis_produk');
            $table->foreign('id_plan')->references('id')->on('plan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('suhu_frayer_1');
    }
}