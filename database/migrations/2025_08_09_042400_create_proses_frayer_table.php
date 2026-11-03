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
        Schema::create('proses_frayer', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->foreignId('id_waktu_penggorengan')->constrained('waktu_penggorengan')->onDelete('cascade');
            $table->foreignId('id_suhu_frayer_1')->constrained('suhu_frayer_1')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->string('aktual_penggorengan');
            $table->string('tpm_minyak');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proses_frayer');
    }
};
