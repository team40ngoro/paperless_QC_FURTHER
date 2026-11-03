<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoKodeProduksiSekunderToDokumentasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dokumentasi', function (Blueprint $table) {
            $table->string('foto_kode_produksi_sekunder')->nullable()->after('foto_kode_produksi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dokumentasi', function (Blueprint $table) {
            $table->dropColumn('foto_kode_produksi_sekunder');
        });
    }
}
