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
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
                 $table->string('kode_form')->nullable()->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
              $table->dropColumn('kode_form');
        });
    }
};
