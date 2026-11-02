<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        try {
            DB::statement('ALTER TABLE pembuatan_sample MODIFY id_produk BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {
            $fkName = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pembuatan_sample' AND COLUMN_NAME = 'id_produk' AND REFERENCED_TABLE_NAME IS NOT NULL");
            if (!empty($fkName)) {
                $fkName = $fkName[0]->CONSTRAINT_NAME;
                DB::statement("ALTER TABLE pembuatan_sample DROP FOREIGN KEY `{$fkName}`");
                DB::statement('ALTER TABLE pembuatan_sample MODIFY id_produk BIGINT UNSIGNED NULL');
                DB::statement("ALTER TABLE pembuatan_sample ADD CONSTRAINT `{$fkName}` FOREIGN KEY (id_produk) REFERENCES jenis_produk(id) ON DELETE CASCADE");
            }
        }
    }

    public function down()
    {
        try {
            DB::statement('ALTER TABLE pembuatan_sample MODIFY id_produk BIGINT UNSIGNED NOT NULL');
        } catch (\Exception $e) {
        }
    }
};
