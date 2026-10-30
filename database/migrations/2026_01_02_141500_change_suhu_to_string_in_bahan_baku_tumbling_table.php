<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('bahan_baku_tumbling') && Schema::hasColumn('bahan_baku_tumbling', 'suhu')) {
            DB::statement("ALTER TABLE bahan_baku_tumbling MODIFY suhu TEXT NULL");
        }
    }

    public function down()
    {
        if (Schema::hasTable('bahan_baku_tumbling') && Schema::hasColumn('bahan_baku_tumbling', 'suhu')) {
            DB::statement("ALTER TABLE bahan_baku_tumbling MODIFY suhu VARCHAR(255) NULL");
        }
    }
};
