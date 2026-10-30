<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeFormToPersiapanBahanFormingTable extends Migration
{
    public function up()
    {
        Schema::table('persiapan_bahan_forming', function (Blueprint $table) {
            $table->string('kode_form', 50)->nullable()->after('tanggal');
        });
    }

    public function down()
    {
        Schema::table('persiapan_bahan_forming', function (Blueprint $table) {
            $table->dropColumn('kode_form');
        });
    }
}