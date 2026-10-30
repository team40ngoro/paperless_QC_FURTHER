<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop old ENUM and replace with VARCHAR to support JSON array (multiple selections)
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE input_mesin_peralatan MODIFY jenis_produk VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE input_mesin_peralatan MODIFY jenis_produk ENUM('Forming','Non-Forming') NULL");
    }
};
