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
        Schema::table('berat_produk_bag', function (Blueprint $table) {
            $table->integer('line')->after('id_data_bag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('berat_produk_bag', function (Blueprint $table) {
            $table->dropColumn('line');
        });
    }
};
