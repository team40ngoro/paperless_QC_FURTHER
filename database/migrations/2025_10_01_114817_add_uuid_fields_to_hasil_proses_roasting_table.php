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
        Schema::table('hasil_proses_roasting', function (Blueprint $table) {
            $table->string('proses_roasting_fan_uuid')->nullable()->after('uuid');
            $table->string('frayer_uuid')->nullable()->after('proses_roasting_fan_uuid');
            $table->string('breader_uuid')->nullable()->after('frayer_uuid');
            $table->string('battering_uuid')->nullable()->after('breader_uuid');
            $table->string('predust_uuid')->nullable()->after('battering_uuid');
            $table->string('penggorengan_uuid')->nullable()->after('predust_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hasil_proses_roasting', function (Blueprint $table) {
            $table->dropColumn([
                'proses_roasting_fan_uuid',
                'frayer_uuid',
                'breader_uuid',
                'battering_uuid',
                'predust_uuid',
                'penggorengan_uuid'
            ]);
        });
    }
};
