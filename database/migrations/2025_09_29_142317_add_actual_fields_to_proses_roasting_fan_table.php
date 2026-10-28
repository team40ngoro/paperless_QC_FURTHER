<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->string('fan_3')->nullable()->after('fan_2');
            $table->string('fan_4')->nullable()->after('fan_3');
            $table->string('aktual_humadity')->nullable()->after('fan_4');
            $table->string('infra_red')->nullable()->after('aktual_humadity');
            $table->string('conveyor_bandung')->nullable()->after('infra_red');
            $table->string('conveyor_infeed')->nullable()->after('conveyor_bandung');
            $table->string('conveyor_outfeed')->nullable()->after('conveyor_infeed');
            $table->string('conveyor_blok1')->nullable()->after('conveyor_outfeed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->dropColumn([
                'fan_3',
                'fan_4', 
                'aktual_humadity',
                'infra_red',
                'conveyor_bandung',
                'conveyor_infeed',
                'conveyor_outfeed',
                'conveyor_blok1'
            ]);
        });
    }
};
