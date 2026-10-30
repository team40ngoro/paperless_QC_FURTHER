<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `release_produk_finish_goods` MODIFY COLUMN `jumlah_box` INT');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `release_produk_finish_goods` MODIFY COLUMN `jumlah_box` TEXT');
    }
};
