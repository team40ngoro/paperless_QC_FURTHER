<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventaris_qc_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_inventaris_qc');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_list_peralatan')->nullable();
            $table->string('nama_alat');
            $table->integer('jumlah')->default(1);
            $table->string('kondisi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_inventaris_qc')->references('id')->on('inventaris_qc')->onDelete('cascade');
            $table->foreign('id_area')->references('id')->on('input_area')->onDelete('cascade');
            $table->foreign('id_list_peralatan')->references('id')->on('list_peralatan')->onDelete('set null');

            $table->index(['id_inventaris_qc', 'id_area']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_qc_detail');
    }
};
