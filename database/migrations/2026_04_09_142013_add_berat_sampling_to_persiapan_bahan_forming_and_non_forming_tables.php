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
        Schema::table('persiapan_bahan_forming', function (Blueprint $table) {
            $table->string('berat_sampling')->nullable()->after('id_formula');
        });
        Schema::table('persiapan_bahan_non_forming', function (Blueprint $table) {
            $table->string('berat_sampling')->nullable()->after('id_no_formula_non_forming');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persiapan_bahan_forming', function (Blueprint $table) {
            $table->dropColumn('berat_sampling');
        });
        Schema::table('persiapan_bahan_non_forming', function (Blueprint $table) {
            $table->dropColumn('berat_sampling');
        });
    }
};
