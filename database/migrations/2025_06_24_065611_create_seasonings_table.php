<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeasoningsTable extends Migration
{
    public function up()
    {
        Schema::create('seasonings', function (Blueprint $table) {
            $table->id(); // primary key auto increment
            $table->uuid('uuid')->unique(); // uuid unique
            $table->string('nama_rm');
            $table->string('kode_produksi');
            $table->integer('berat');
            $table->string('suhu');
            $table->string('sensori');
            $table->string('kemasan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seasonings');
    }
}