<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verif_peralatan_bagian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verif_peralatan_detail_id');
            $table->unsignedBigInteger('id_bagian');
            $table->boolean('verifikasi')->default(false);
            $table->text('keterangan')->nullable();
            $table->text('tindakan_koreksi')->nullable();
            $table->timestamps();

            $table->foreign('verif_peralatan_detail_id', 'fk_vpb_detail')
                  ->references('id')->on('verif_peralatan_detail')
                  ->onDelete('cascade');
            $table->foreign('id_bagian', 'fk_vpb_bagian')
                  ->references('id')->on('bagian_mesin')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verif_peralatan_bagian');
    }
};
