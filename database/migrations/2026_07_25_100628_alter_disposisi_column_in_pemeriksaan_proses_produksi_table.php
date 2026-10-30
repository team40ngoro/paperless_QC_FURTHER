<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE pemeriksaan_proses_produksi MODIFY COLUMN disposisi VARCHAR(255) NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE pemeriksaan_proses_produksi MODIFY COLUMN disposisi ENUM('reject_musnahkan','rework','rework_perlakuan','repack','sortir') NULL");
    }
};