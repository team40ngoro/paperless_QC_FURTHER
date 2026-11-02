<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verif_peralatan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_shift');
            $table->date('tanggal');

            $table->timestamps();

            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_shift')->references('id')->on('data_shift')->onDelete('cascade');

            $table->index(['id_plan', 'id_shift', 'tanggal']);
        });

        Schema::create('verif_peralatan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verif_peralatan_id');

            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_sub_area');

            $table->boolean('verifikasi')->default(false);
            $table->text('keterangan')->nullable();
            $table->text('tindakan_koreksi')->nullable();

            $table->timestamps();

            $table->foreign('verif_peralatan_id')->references('id')->on('verif_peralatan')->onDelete('cascade');
            $table->foreign('id_mesin')->references('id')->on('input_mesin_peralatan')->onDelete('cascade');
            $table->foreign('id_area')->references('id')->on('input_area')->onDelete('cascade');
            $table->foreign('id_sub_area')->references('id')->on('sub_area')->onDelete('cascade');

            $table->unique(['verif_peralatan_id', 'id_mesin', 'id_sub_area'], 'vp_detail_unique');
            $table->index(['id_area', 'id_mesin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verif_peralatan_detail');
        Schema::dropIfExists('verif_peralatan');
    }
};
