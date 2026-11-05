<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * E-QF 15 (Wizard Packing) kini diisi bertahap oleh dua role:
 *  - Produksi membuat record (Identitas, Waktu, Plastik, Aktual Isi Karton)
 *  - QC melengkapi verifikasi (Suhu, Verifikasi Plastik/Karton, Berat Bag/Box, Dokumentasi, Release)
 * Karena itu kolom milik QC harus boleh NULL saat record dibuat oleh Produksi.
 * Tipe kolom dipertahankan apa adanya (hanya constraint NOT NULL yang dilepas).
 */
return new class extends Migration
{
    private array $columns = [
        'pengemasan_produk' => ['aktual_suhu_produk', 'waktu_awal_packing', 'status_release_produk', 'tindakan_koreksi'],
        'pengemasan_plastik' => ['identitas_produk', 'kesesuaian_label_plastik', 'kekuatan_seal'],
        'pengemasan_karton' => ['identitas_produk_pada_karton', 'standar_jumlah_karton', 'kesesuaian_isi_pack', 'id_berat_produk_bag', 'id_berat_produk_box'],
        'berat_produk_bag' => ['id_data_bag', 'berat_aktual', 'rata_rata_berat'],
        'berat_produk_box' => ['id_data_box', 'berat_aktual', 'rata_rata_berat', 'id_berat_produk_bag'],
        'dokumentasi' => [
            'foto_kode_produksi', 'foto_kode_produksi_sekunder', 'qr_code', 'label_polyroll', 'label_supplier',
            'keterangan', 'id_pengemasan_plastik', 'id_berat_produk_bag', 'id_berat_produk_box', 'id_pengemasan_karton',
        ],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $database = DB::getDatabaseName();

        foreach ($this->columns as $table => $cols) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($cols as $col) {
                if (! Schema::hasColumn($table, $col)) {
                    continue;
                }

                $info = DB::selectOne(
                    'SELECT COLUMN_TYPE AS column_type, IS_NULLABLE AS is_nullable FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$database, $table, $col]
                );

                if (! $info || $info->is_nullable === 'YES') {
                    continue;
                }

                DB::statement("ALTER TABLE `{$table}` MODIFY `{$col}` {$info->column_type} NULL DEFAULT NULL");
            }
        }
    }

    public function down(): void
    {
        // Tidak di-rollback: mengembalikan NOT NULL akan gagal bila sudah ada data parsial.
    }
};

