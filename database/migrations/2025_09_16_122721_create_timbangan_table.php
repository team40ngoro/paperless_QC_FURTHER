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
        if (!Schema::hasTable('timbangan')) {
            Schema::create('timbangan', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('id_plan');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('shift_id');
                $table->datetime('tanggal');
                $table->string('jenis');
                $table->string('kode_thermometer');
                $table->enum('hasil_pengecekan', ['ok', 'tidak_ok']);
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

                // Indexes untuk performa
                $table->index(['id_plan', 'tanggal']);
                $table->index('user_id');
                $table->index('shift_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timbangan');
    }
};
