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
         Schema::create('no_formula_non_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_produk');
            $table->string('nomor_formula');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_plan');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('produk_non_forming');
    }
};
