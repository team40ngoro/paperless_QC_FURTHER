<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produk_yum', function (Blueprint $table) {
            $table->string('kode_exp')->nullable()->after('kode_produksi');
        });
    }

    public function down()
    {
        Schema::table('produk_yum', function (Blueprint $table) {
            $table->dropColumn('kode_exp');
        });
    }
};