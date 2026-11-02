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
        Schema::table('pembuatan_emulsi', function (Blueprint $table) {
            $table->string('kondisi')->nullable()->after('hasil_emulsi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembuatan_emulsi', function (Blueprint $table) {
            $table->dropColumn('kondisi');
        });
    }
};
