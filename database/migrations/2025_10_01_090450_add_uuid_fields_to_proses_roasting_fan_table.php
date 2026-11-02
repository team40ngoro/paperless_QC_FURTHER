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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('proses_roasting_fan', 'frayer_uuid')) {
                $table->string('frayer_uuid')->nullable()->after('block_number');
            }
            if (!Schema::hasColumn('proses_roasting_fan', 'breader_uuid')) {
                $table->string('breader_uuid')->nullable()->after('frayer_uuid');
            }
            if (!Schema::hasColumn('proses_roasting_fan', 'battering_uuid')) {
                $table->string('battering_uuid')->nullable()->after('breader_uuid');
            }
            if (!Schema::hasColumn('proses_roasting_fan', 'predust_uuid')) {
                $table->string('predust_uuid')->nullable()->after('battering_uuid');
            }
            if (!Schema::hasColumn('proses_roasting_fan', 'penggorengan_uuid')) {
                $table->string('penggorengan_uuid')->nullable()->after('predust_uuid');
            }
            
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
            $table->dropColumn(['frayer_uuid', 'breader_uuid', 'battering_uuid', 'predust_uuid', 'penggorengan_uuid']);
        });
    }
};
