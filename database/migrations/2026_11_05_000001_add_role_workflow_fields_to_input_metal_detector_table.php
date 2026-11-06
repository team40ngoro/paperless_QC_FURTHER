<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('input_metal_detector', function (Blueprint $table) {
            // Bagian B (Hasil Pengecekan / Proses Produksi)
            $table->string('merk')->nullable()->after('line');
            $table->time('waktu_mulai')->nullable()->after('jam');
            $table->time('waktu_selesai')->nullable()->after('waktu_mulai');
            $table->string('gramasi')->nullable()->after('berat_produk');
            $table->integer('jumlah_reject')->nullable()->default(0)->after('keterangan');
            $table->string('nama_operator')->nullable()->after('jumlah_reject');
            $table->string('spv_foreman')->nullable()->after('nama_operator');
            $table->text('keterangan_b')->nullable()->after('spv_foreman');

            // Verifikasi QC
            $table->string('verifikasi_detail')->nullable()->after('keterangan_b');
            $table->string('verifikasi_hasil_proses')->nullable()->after('verifikasi_detail');
            $table->string('nama_qc')->nullable()->after('verifikasi_hasil_proses');
            $table->timestamp('qc_verified_at')->nullable()->after('nama_qc');
        });
    }

    public function down(): void
    {
        Schema::table('input_metal_detector', function (Blueprint $table) {
            $table->dropColumn([
                'merk',
                'waktu_mulai',
                'waktu_selesai',
                'gramasi',
                'jumlah_reject',
                'nama_operator',
                'spv_foreman',
                'keterangan_b',
                'verifikasi_detail',
                'verifikasi_hasil_proses',
                'nama_qc',
                'qc_verified_at',
            ]);
        });
    }
};

