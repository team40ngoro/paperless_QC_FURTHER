<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuhuEmulsiTable extends Migration
{
    public function up()
    {
        Schema::create('suhu_emulsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_emulsi_id')->constrained('bahan_emulsi')->onDelete('cascade');
            $table->foreignId('pembuatan_emulsi_id')->constrained('pembuatan_emulsi')->onDelete('cascade');
            $table->string('suhu');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suhu_emulsi');
    }
}