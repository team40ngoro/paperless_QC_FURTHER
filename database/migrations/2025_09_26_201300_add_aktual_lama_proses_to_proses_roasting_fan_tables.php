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
        // Add aktual_lama_proses to proses_roasting_fan table
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->string('aktual_lama_proses')->nullable()->after('fan_2');
        });

        // Add aktual_lama_proses_2 to proses_roasting_fan_2 table
        Schema::table('proses_roasting_fan_2', function (Blueprint $table) {
            $table->string('aktual_lama_proses_2')->nullable()->after('fan_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->dropColumn('aktual_lama_proses');
        });

        Schema::table('proses_roasting_fan_2', function (Blueprint $table) {
            $table->dropColumn('aktual_lama_proses_2');
        });
    }
};
