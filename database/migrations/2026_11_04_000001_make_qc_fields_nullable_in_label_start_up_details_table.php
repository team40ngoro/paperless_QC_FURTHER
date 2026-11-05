<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * E-QF 36 (Pemeriksaan Labelisasi Start Up) kini diisi bertahap oleh dua role:
 *  - Produksi membuat record (Tanggal, Shift, Plant, Detail Produk, Jam, Foto, PIC Operator, Tindakan Koreksi)
 *  - QC Inspector menginput verifikasi hasil (OK / Tidak OK) melalui icon mata (Show)
 * Karena itu kolom hasil_pengecekan, nama_qc, dan ttd_qc harus boleh NULL saat record dibuat oleh Produksi.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            if (Schema::hasTable('label_start_up_details')) {
                DB::statement("ALTER TABLE `label_start_up_details` MODIFY `hasil_pengecekan` ENUM('OK', 'Tidak OK') NULL DEFAULT NULL");
                DB::statement("ALTER TABLE `label_start_up_details` MODIFY `nama_qc` VARCHAR(255) NULL DEFAULT NULL");
                DB::statement("ALTER TABLE `label_start_up_details` MODIFY `ttd_qc` LONGTEXT NULL DEFAULT NULL");
            }
        }
    }

    public function down(): void
    {
        // Tetap nullable untuk menjaga kompatibilitas data parsial
    }
};

