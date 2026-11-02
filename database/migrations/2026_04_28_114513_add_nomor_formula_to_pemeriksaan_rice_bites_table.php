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
        Schema::table('pemeriksaan_rice_bites', function (Blueprint $table) {
            $table->string('nomor_formula')->nullable()->after('id_produk');
        });
    }

    public function down()
    {
        Schema::table('pemeriksaan_rice_bites', function (Blueprint $table) {
            $table->dropColumn('nomor_formula');
        });
    }
};
