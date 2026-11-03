<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE proses_battering MODIFY id_jenis_better VARCHAR(255) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE proses_battering MODIFY id_jenis_better BIGINT UNSIGNED NULL');
    }
};
