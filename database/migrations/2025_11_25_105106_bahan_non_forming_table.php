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
      Schema::create('bahan_rm_non_forming', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_no_formula_non_forming');
            $table->string('nama_rm');
            $table->decimal('berat_rm', 10, 2);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_plan');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_no_formula_non_forming')->references('id')->on('no_formula_non_forming')->onDelete('cascade');
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
        Schema::dropIfExists('bahan_rm_non_forming');
    }
};
