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
        $tables = ['proses_roasting_fan', 'hasil_proses_roasting', 'pembekuan_iqf_roasting'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Frayer 2 might already exist in some tables, but not in others. 
                // Listing shows it was missing in all three for Wizard 2.
                if (!Schema::hasColumn($table->getTable(), 'frayer2_uuid')) {
                    $table->string('frayer2_uuid')->nullable()->after('frayer_uuid');
                }
                $table->string('frayer3_uuid')->nullable()->after('frayer2_uuid');
                $table->string('frayer4_uuid')->nullable()->after('frayer3_uuid');
                $table->string('frayer5_uuid')->nullable()->after('frayer4_uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['proses_roasting_fan', 'hasil_proses_roasting', 'pembekuan_iqf_roasting'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['frayer2_uuid', 'frayer3_uuid', 'frayer4_uuid', 'frayer5_uuid']);
            });
        }
    }
};
