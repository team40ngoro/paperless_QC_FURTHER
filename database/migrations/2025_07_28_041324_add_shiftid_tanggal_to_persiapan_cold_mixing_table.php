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
    Schema::table('persiapan_cold_mixing', function (Blueprint $table) {
        $table->unsignedBigInteger('shift_id')->after('id_plan')->nullable();
        $table->date('tanggal')->after('shift_id')->nullable();
        $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('persiapan_cold_mixing', function (Blueprint $table) {
        $table->dropForeign(['shift_id']);
        $table->dropColumn(['shift_id', 'tanggal']);
    });
}
};
