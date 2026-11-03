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
        // Add to pembekuan_iqf_penggorengan
        Schema::table('pembekuan_iqf_penggorengan', function (Blueprint $table) {
            $table->string('frayer3_uuid')->nullable()->after('frayer2_uuid');
            $table->string('frayer4_uuid')->nullable()->after('frayer3_uuid');
            $table->string('frayer5_uuid')->nullable()->after('frayer4_uuid');
        });

        // Add to hasil_penggorengan
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->string('frayer3_uuid')->nullable()->after('frayer2_uuid');
            $table->string('frayer4_uuid')->nullable()->after('frayer3_uuid');
            $table->string('frayer5_uuid')->nullable()->after('frayer4_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembekuan_iqf_penggorengan', function (Blueprint $table) {
            $table->dropColumn(['frayer3_uuid', 'frayer4_uuid', 'frayer5_uuid']);
        });

        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->dropColumn(['frayer3_uuid', 'frayer4_uuid', 'frayer5_uuid']);
        });
    }
};
