<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('persiapan_bahan_non_forming', function (Blueprint $table) {
            if (Schema::hasColumn('persiapan_bahan_non_forming', 'kode_produksi_emulsi')) {
                $table->dropColumn('kode_produksi_emulsi');
            }
        });
    }

    public function down()
    {
        Schema::table('persiapan_bahan_non_forming', function (Blueprint $table) {
            if (!Schema::hasColumn('persiapan_bahan_non_forming', 'kode_produksi_emulsi')) {
                $table->string('kode_produksi_emulsi')->nullable()->after('kode_produksi');
            }
        });
    }
};
