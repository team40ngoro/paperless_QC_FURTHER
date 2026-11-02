<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisEmulsiTable extends Migration
{
    public function up()
    {
        Schema::create('jenis_emulsi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama_emulsi');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_produk');
            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('id_produk')->references('id')->on('jenis_produk');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jenis_emulsi');
    }
}