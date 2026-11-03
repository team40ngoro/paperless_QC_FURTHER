<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE proses_battering MODIFY kode_produksi_better TEXT NULL');
        DB::statement('ALTER TABLE proses_breader MODIFY kode_produksi TEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE proses_battering MODIFY kode_produksi_better VARCHAR(255) NULL');
        DB::statement('ALTER TABLE proses_breader MODIFY kode_produksi VARCHAR(255) NOT NULL');
    }
};
