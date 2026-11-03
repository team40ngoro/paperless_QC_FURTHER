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
        Schema::table('pemeriksaan_rheon_machine', function (Blueprint $table) {
            $table->dropColumn('outlet_cvg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemeriksaan_rheon_machine', function (Blueprint $table) {
            $table->string('outlet_cvg')->nullable()->after('jenis_cetakan');
        });
    }
};
