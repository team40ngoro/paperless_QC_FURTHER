<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBetterItemsToJenisBetterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_better', function (Blueprint $table) {
            $table->json('better_items')->nullable()->after('nama_formula_better');
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
            $table->dropColumn('better_items');
        });
    }
}
