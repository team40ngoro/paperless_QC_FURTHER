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
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            $table->string('kode_form', 50)->nullable()->after('kode_produksi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            $table->dropColumn('kode_form');
        });
    }
};
