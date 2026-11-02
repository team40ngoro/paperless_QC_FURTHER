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
        Schema::create('input_metal_detector', function (Blueprint $table) {
            $table->id();
                $table->uuid('uuid')->unique();
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('data_shift')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('berat_produk');
            $table->string('kode_produksi');
            $table->string('fe_depan_aktual');
            $table->string('fe_tengah_aktual');
            $table->string('fe_belakang_aktual');
            $table->string('non_fe_depan_aktual');
            $table->string('non_fe_tengah_aktual');
            $table->string('non_fe_belakang_aktual');
            $table->string('sus_depan_aktual');
            $table->string('sus_tengah_aktual');
            $table->string('sus_belakang_aktual');
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
        Schema::dropIfExists('input_metal_detector');
    }
};
