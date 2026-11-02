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
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->string('aktual_waktu')->nullable()->after('waktu_pemasakan');
            $table->string('aktual_suhu_fan')->nullable()->after('aktual_waktu');
            $table->string('aktual_speed_fan')->nullable()->after('aktual_suhu_fan');
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
            $table->dropColumn(['aktual_waktu', 'aktual_suhu_fan', 'aktual_speed_fan']);
        });
    }
};
