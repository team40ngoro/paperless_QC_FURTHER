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
        DB::statement('ALTER TABLE `release_produk_finish_goods` MODIFY COLUMN `berat_packing_sekunder` TEXT');
        DB::statement('ALTER TABLE `release_produk_finish_goods` MODIFY COLUMN `jumlah_box` TEXT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `release_produk_finish_goods` MODIFY COLUMN `berat_packing_sekunder` DECIMAL(10, 2)');
        DB::statement('ALTER TABLE `release_produk_finish_goods` MODIFY COLUMN `jumlah_box` INT');
    }
};
