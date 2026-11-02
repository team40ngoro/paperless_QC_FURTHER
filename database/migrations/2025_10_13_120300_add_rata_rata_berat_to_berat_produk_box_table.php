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
        Schema::table('berat_produk_box', function (Blueprint $table) {
            $table->double('rata_rata_berat', 8, 2)->nullable()->after('berat_aktual_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('berat_produk_box', function (Blueprint $table) {
            $table->dropColumn('rata_rata_berat');
        });
    }
};
