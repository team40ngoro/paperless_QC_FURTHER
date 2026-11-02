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
            $table->dropForeign('bahan_baku_tumbling_id_bahan_nonforming_foreign');
            $table->foreign('id_bahan_nonforming')->references('id')->on('no_formula_non_forming')->onDelete('set null');
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
            $table->dropForeign(['id_bahan_nonforming']);
            $table->foreign('id_bahan_nonforming')->references('id')->on('bahan_rm_non_forming')->onDelete('set null');
        });
    }
};
