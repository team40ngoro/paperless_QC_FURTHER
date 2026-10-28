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
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->unsignedBigInteger('id_formula')->nullable()->after('id_produk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->dropColumn('id_formula');
        });
    }
};
