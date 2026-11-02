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
        Schema::table('std_fan', function (Blueprint $table) {
            $table->string('std_lama_proses')->nullable()->after('std_fan_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('std_fan', function (Blueprint $table) {
            $table->dropColumn('std_lama_proses');
        });
    }
};
