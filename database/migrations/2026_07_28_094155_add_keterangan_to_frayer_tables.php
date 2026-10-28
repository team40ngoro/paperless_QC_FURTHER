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
        Schema::table('proses_frayer', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('frayer_2', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('frayer_3', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('frayer_4', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('frayer_5', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('proses_frayer', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('frayer_2', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('frayer_3', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('frayer_4', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('frayer_5', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
