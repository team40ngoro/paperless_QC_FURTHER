<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengemasan_produk', function (Blueprint $table) {
            if (!Schema::hasColumn('pengemasan_produk', 'suhu_iqf')) {
                $table->string('suhu_iqf')->nullable()->after('std_suhu_produk_iqf');
            }
            if (!Schema::hasColumn('pengemasan_produk', 'verifikasi_akhir_pengemasan')) {
                $table->string('verifikasi_akhir_pengemasan')->nullable()->after('status_release_produk');
            }
        });

        // Pastikan waktu_awal_packing dan tanggal_expired nullable & fleksibel
        DB::statement('ALTER TABLE pengemasan_produk MODIFY waktu_awal_packing VARCHAR(100) NULL');
        DB::statement('ALTER TABLE pengemasan_produk MODIFY tanggal_expired DATE NULL');

        Schema::table('pengemasan_karton', function (Blueprint $table) {
            if (!Schema::hasColumn('pengemasan_karton', 'hasil_finish_good_box_pallet')) {
                $table->string('hasil_finish_good_box_pallet')->nullable()->after('aktual_jumlah_karton');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'total_reject_produk')) {
                $table->string('total_reject_produk')->nullable()->after('hasil_finish_good_box_pallet');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'reject_produk_mp')) {
                $table->string('reject_produk_mp')->nullable()->after('total_reject_produk');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'reject_produk_cooking')) {
                $table->string('reject_produk_cooking')->nullable()->after('reject_produk_mp');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'reject_produk_iqf')) {
                $table->string('reject_produk_iqf')->nullable()->after('reject_produk_cooking');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'reject_produk_packing')) {
                $table->string('reject_produk_packing')->nullable()->after('reject_produk_iqf');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'total_reject_kemasan')) {
                $table->string('total_reject_kemasan')->nullable()->after('reject_produk_packing');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'total_rework_produk')) {
                $table->string('total_rework_produk')->nullable()->after('total_reject_kemasan');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'rework_produk_mp')) {
                $table->string('rework_produk_mp')->nullable()->after('total_rework_produk');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'rework_produk_cooking')) {
                $table->string('rework_produk_cooking')->nullable()->after('rework_produk_mp');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'rework_produk_iqf')) {
                $table->string('rework_produk_iqf')->nullable()->after('rework_produk_cooking');
            }
            if (!Schema::hasColumn('pengemasan_karton', 'rework_produk_packing')) {
                $table->string('rework_produk_packing')->nullable()->after('rework_produk_iqf');
            }
        });

        DB::statement('ALTER TABLE pengemasan_karton MODIFY identitas_produk_pada_karton VARCHAR(255) NULL');
        DB::statement('ALTER TABLE pengemasan_karton MODIFY standar_jumlah_karton VARCHAR(255) NULL');
        DB::statement('ALTER TABLE pengemasan_karton MODIFY aktual_jumlah_karton VARCHAR(255) NULL');
    }

    public function down(): void
    {
        Schema::table('pengemasan_produk', function (Blueprint $table) {
            $cols = array_filter(['suhu_iqf', 'verifikasi_akhir_pengemasan'], fn($c) => Schema::hasColumn('pengemasan_produk', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('pengemasan_karton', function (Blueprint $table) {
            $cols = array_filter([
                'hasil_finish_good_box_pallet',
                'total_reject_produk',
                'reject_produk_mp',
                'reject_produk_cooking',
                'reject_produk_iqf',
                'reject_produk_packing',
                'total_reject_kemasan',
                'total_rework_produk',
                'rework_produk_mp',
                'rework_produk_cooking',
                'rework_produk_iqf',
                'rework_produk_packing',
            ], fn($c) => Schema::hasColumn('pengemasan_karton', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

