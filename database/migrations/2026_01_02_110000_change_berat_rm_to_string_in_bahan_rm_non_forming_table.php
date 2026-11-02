<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('bahan_rm_non_forming') && Schema::hasColumn('bahan_rm_non_forming', 'berat_rm')) {
            DB::statement("ALTER TABLE bahan_rm_non_forming MODIFY berat_rm VARCHAR(50) NOT NULL");
        }
    }

    public function down()
    {
        if (Schema::hasTable('bahan_rm_non_forming') && Schema::hasColumn('bahan_rm_non_forming', 'berat_rm')) {
            DB::statement("ALTER TABLE bahan_rm_non_forming MODIFY berat_rm DECIMAL(10,2) NOT NULL");
        }
    }
};
