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
        Schema::table('thermometer', function (Blueprint $table) {
            $table->string('hasil_verifikasi_0')->nullable()->after('jam');
            $table->string('hasil_verifikasi_100')->nullable()->after('hasil_verifikasi_0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thermometer', function (Blueprint $table) {
            $table->dropColumn(['hasil_verifikasi_0', 'hasil_verifikasi_100']);
        });
    }
};
