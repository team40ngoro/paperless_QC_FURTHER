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
        Schema::table('label_start_up_details', function (Blueprint $table) {
            $table->string('label_produk')->after('id_produk')->nullable();
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE label_start_up_details MODIFY COLUMN foto_kode_produksi VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('label_start_up_details', function (Blueprint $table) {
            $table->dropColumn('label_produk');
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE label_start_up_details MODIFY COLUMN foto_kode_produksi VARCHAR(255) NOT NULL');
    }
};
