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
        Schema::table('produk_non_forming', function (Blueprint $table) {
            $table->unsignedBigInteger('id_no_formula_non_forming')->nullable()->after('id_produk');
            
            // Foreign key relationship
            $table->foreign('id_no_formula_non_forming')->references('id')->on('no_formula_non_forming')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_non_forming', function (Blueprint $table) {
            $table->dropForeign(['id_no_formula_non_forming']);
            $table->dropColumn('id_no_formula_non_forming');
        });
    }
};
