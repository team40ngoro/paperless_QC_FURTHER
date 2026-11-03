<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE pemeriksaan_benda_asing MODIFY id_produk BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE pemeriksaan_benda_asing MODIFY id_produk BIGINT UNSIGNED NOT NULL');
    }
};
