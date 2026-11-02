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
        Schema::create('proses_marinade_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('proses_marinade_id');
            $table->uuid('proses_marinade_uuid');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_role');
            $table->string('aksi')->default('update');
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['proses_marinade_id']);
            $table->index(['proses_marinade_uuid']);
            $table->index(['user_id']);
            $table->index(['created_at']);

            $table->foreign('proses_marinade_id')->references('id')->on('proses_marinade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proses_marinade_logs');
    }
};
