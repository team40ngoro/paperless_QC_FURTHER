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
    Schema::create('total_pemakaian_emulsi', function (Blueprint $table) {
        $table->id(); // auto increment primary key, jangan pakai parameter
        $table->uuid('uuid')->unique();
        $table->double('total_pemakaian');
        $table->unsignedBigInteger('nama_emulsi_id');
        $table->unsignedBigInteger('id_plan');
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('id_produk');
        $table->timestamps();

        $table->foreign('nama_emulsi_id')->references('id')->on('jenis_emulsi')->onDelete('cascade');
        $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
    });
}
};
