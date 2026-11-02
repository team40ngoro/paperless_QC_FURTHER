<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE gmp_karyawan MODIFY COLUMN temuan_ketidaksesuaian ENUM('sesuai', 'perlengkapan', 'kuku', 'perhiasan', 'luka')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()  
    {
        DB::statement("ALTER TABLE gmp_karyawan MODIFY COLUMN temuan_ketidaksesuaian ENUM('perlengkapan', 'kuku', 'perhiasan', 'luka')");
    }
};
