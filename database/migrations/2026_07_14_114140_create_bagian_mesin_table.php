<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bagian_mesin', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('id_mesin');
            $table->string('nama_bagian');

            $table->timestamps();

            $table->foreign('id_mesin')->references('id')->on('input_mesin_peralatan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bagian_mesin');
    }
};
