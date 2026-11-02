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
        Schema::create('verifikasi_berat_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('verifikasi_berat_id');
            $table->uuid('verifikasi_berat_uuid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name');
            $table->string('user_role');
            $table->string('aksi'); // create, update, delete
            $table->json('field_yang_diubah')->nullable();
            $table->json('nilai_lama')->nullable();
            $table->json('nilai_baru')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['verifikasi_berat_id', 'created_at']);
            $table->index('verifikasi_berat_uuid');
            $table->index('user_id');
            $table->index('aksi');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifikasi_berat_logs');
    }
};
