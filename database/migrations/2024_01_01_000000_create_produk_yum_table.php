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
        Schema::create('produk_yum', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_data_bag');
            $table->string('kode_produksi');
            $table->text('berat_pcs'); // JSON array stored as text
            $table->text('jumlah_pcs'); // JSON array stored as text
            $table->text('aktual_berat'); // JSON array stored as text
            $table->datetime('tanggal');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_data_bag')->references('id')->on('data_bag')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_yum');
    }
};
