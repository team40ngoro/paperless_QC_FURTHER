<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            if (Schema::hasTable('penggorengan')) {
                DB::statement('ALTER TABLE `penggorengan` MODIFY `hasil_pencetakan` VARCHAR(255) NULL');
            }
            if (Schema::hasTable('pembuatan_predust')) {
                DB::statement('ALTER TABLE `pembuatan_predust` MODIFY `kondisi_predust` VARCHAR(255) NULL');
                DB::statement('ALTER TABLE `pembuatan_predust` MODIFY `hasil_pencetakan` VARCHAR(255) NULL');
            }
            if (Schema::hasTable('proses_battering')) {
                DB::statement('ALTER TABLE `proses_battering` MODIFY `hasil_better` VARCHAR(255) NULL');
            }
            if (Schema::hasTable('proses_breader')) {
                DB::statement('ALTER TABLE `proses_breader` MODIFY `hasil_breader` VARCHAR(255) NULL');
            }
            if (Schema::hasTable('pembekuan_iqf_penggorengan')) {
                DB::statement('ALTER TABLE `pembekuan_iqf_penggorengan` MODIFY `suhu_ruang_iqf` VARCHAR(255) NULL');
                DB::statement('ALTER TABLE `pembekuan_iqf_penggorengan` MODIFY `holding_time` VARCHAR(255) NULL');
            }
        }
    }

    public function down(): void
    {
        // Biarkan tetap nullable untuk menjaga kompatibilitas data yang sudah masuk
    }
};
