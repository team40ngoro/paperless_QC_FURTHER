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
            $table->text('keterangan')->nullable()->after('hasil_pencampuran');
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
            $table->dropColumn('keterangan');
        });
    }
};
