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
        Schema::table('bahan_formula_fla', function (Blueprint $table) {
            $table->dropColumn('proses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_formula_fla', function (Blueprint $table) {
            $table->string('proses')->nullable()->after('id_nomor_step_formula_fla');
        });
    }
};
