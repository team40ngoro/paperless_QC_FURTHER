<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeratAndNamaFormulaBetterToJenisBetterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_better', function (Blueprint $table) {
            $table->string('berat')->nullable()->after('nama_better');
            $table->string('nama_formula_better')->nullable()->after('berat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jenis_better', function (Blueprint $table) {
            $table->dropColumn(['berat', 'nama_formula_better']);
        });
    }
}
