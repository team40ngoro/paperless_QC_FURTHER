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
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('gmp_karyawan')) {
            Schema::create('gmp_karyawan', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('id_plan');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('id_area');
                $table->unsignedBigInteger('shift_id');
                $table->datetime('tanggal');
                $table->string('nama_karyawan');
                $table->enum('temuan_ketidaksesuaian', ['perlengkapan', 'kuku', 'perhiasan', 'luka']);
                $table->text('keterangan')->nullable();
                $table->text('tindakan_koreksi')->nullable();
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('id_area')->references('id')->on('input_area')->onDelete('cascade');
                $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

                // Indexes untuk performa
                $table->index(['id_plan', 'tanggal']);
                $table->index('user_id');
                $table->index('shift_id');
                $table->index('id_area');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gmp_karyawan');
    }
};
