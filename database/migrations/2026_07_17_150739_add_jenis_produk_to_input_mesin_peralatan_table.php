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
        Schema::table('input_mesin_peralatan', function (Blueprint $table) {
            $table->enum('jenis_produk', ['Forming', 'Non-Forming'])->nullable()->after('nama_mesin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('input_mesin_peralatan', function (Blueprint $table) {
            $table->dropColumn('jenis_produk');
        });
    }
};
