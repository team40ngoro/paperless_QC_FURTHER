<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY id_formula BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY id_formula BIGINT UNSIGNED NOT NULL');
    }
};
