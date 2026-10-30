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
        Schema::table('frayer_3', function (Blueprint $table) {
            $table->string('penggorengan_uuid')->nullable()->after('uuid');
            $table->string('predust_uuid')->nullable()->after('penggorengan_uuid');
            $table->string('battering_uuid')->nullable()->after('predust_uuid');
            $table->string('breader_uuid')->nullable()->after('battering_uuid');
            
            $table->index('penggorengan_uuid');
            $table->index('predust_uuid');
            $table->index('battering_uuid');
            $table->index('breader_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frayer_3', function (Blueprint $table) {
            $table->dropIndex(['penggorengan_uuid']);
            $table->dropIndex(['predust_uuid']);
            $table->dropIndex(['battering_uuid']);
            $table->dropIndex(['breader_uuid']);
            
            $table->dropColumn(['penggorengan_uuid', 'predust_uuid', 'battering_uuid', 'breader_uuid']);
        });
    }
};
