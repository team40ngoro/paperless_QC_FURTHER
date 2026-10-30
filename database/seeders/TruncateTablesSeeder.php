<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTablesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚨 PERINGATAN: Seeder ini akan menghapus semua data di database kecuali tabel tertentu.');
        if (! $this->command->confirm('Apakah kamu yakin ingin melanjutkan truncate semua tabel?', false)) {
            $this->command->info('❌ Dibatalkan oleh pengguna.');
            return;
        }

        // Nonaktifkan foreign key constraints
        Schema::disableForeignKeyConstraints();

        // Ambil semua nama tabel dari database aktif
        $tables = DB::select('SHOW TABLES');
        $dbName = 'Tables_in_' . env('DB_DATABASE');

        // Daftar tabel yang tidak ingin dihapus
        $excludedTables = ['users', 'roles', 'migrations'];

        foreach ($tables as $table) {
            $tableName = $table->$dbName;

            if (in_array($tableName, $excludedTables)) {
                $this->command->info("⏭️ Melewati tabel: {$tableName}");
                continue;
            }

            try {
                DB::table($tableName)->truncate();
                $this->command->info("✅ Berhasil truncate tabel: {$tableName}");
            } catch (\Exception $e) {
                $this->command->error("⚠️ Gagal truncate tabel: {$tableName} — {$e->getMessage()}");
            }
        }

        // Aktifkan kembali constraints
        Schema::enableForeignKeyConstraints();

        $this->command->info('🎉 Semua tabel berhasil di-truncate (kecuali yang dikecualikan).');
    }
}
