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
        Schema::table('suhu_forming', function (Blueprint $table) {
            $table->string('sensori')->nullable()->after('kode_produksi_bahan');
            $table->text('keterangan')->nullable()->after('sensori');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suhu_forming', function (Blueprint $table) {
            $table->dropColumn(['sensori', 'keterangan']);
        });
    }
};
