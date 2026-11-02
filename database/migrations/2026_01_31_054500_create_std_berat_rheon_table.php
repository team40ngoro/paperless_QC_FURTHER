<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('std_berat_rheon', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_produk');
            $table->string('std_rheon');

            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('std_berat_rheon');
    }
};
