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
        Schema::create('aktual_better', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_std_salinitas_viskositas');
            $table->unsignedBigInteger('id_persiapan_bahan_better');
            $table->string('aktual_vis');
            $table->string('aktual_sal');
            $table->string('aktual_suhu_air');
            $table->timestamps();

            $table->foreign('id_std_salinitas_viskositas')->references('id')->on('std_salinitas_viskositas');
            $table->foreign('id_persiapan_bahan_better')->references('id')->on('persiapan_bahan_better');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktual_better');
    }
};
