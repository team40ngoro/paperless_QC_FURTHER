<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom suhu_frayer_2 dan waktu_penggorengan_2
     * ke tabel suhu_frayer_1 agar kedua modul bisa digabungkan.
     */
    public function up(): void
    {
        Schema::table('suhu_frayer_1', function (Blueprint $table) {
            // Tambah setelah kolom waktu_penggorengan_1
            $table->string('suhu_frayer_2')->nullable()->after('waktu_penggorengan_1')->comment('STD Suhu Frayer 2');
            $table->string('waktu_penggorengan_2')->nullable()->after('suhu_frayer_2')->comment('STD Waktu Penggorengan Frayer 2 (detik)');
        });
    }

    public function down(): void
    {
        Schema::table('suhu_frayer_1', function (Blueprint $table) {
            $table->dropColumn(['suhu_frayer_2', 'waktu_penggorengan_2']);
        });
    }
};
