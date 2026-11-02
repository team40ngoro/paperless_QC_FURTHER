<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proses_marinade', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_shift');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_jenis_marinade');
            $table->string('kode_produksi');
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal');
            $table->text('hasil_pencampuran');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_jenis_marinade')->references('id')->on('jenis_marinade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proses_marinade');
    }
};
