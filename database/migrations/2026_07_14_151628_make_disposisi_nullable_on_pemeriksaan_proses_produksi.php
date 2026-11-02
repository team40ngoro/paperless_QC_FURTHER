<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE pemeriksaan_proses_produksi MODIFY COLUMN disposisi ENUM('reject_musnahkan', 'rework', 'rework_perlakuan', 'repack', 'sortir') NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE pemeriksaan_proses_produksi MODIFY COLUMN disposisi ENUM('reject_musnahkan', 'rework', 'rework_perlakuan', 'repack', 'sortir') NOT NULL");
    }
};
