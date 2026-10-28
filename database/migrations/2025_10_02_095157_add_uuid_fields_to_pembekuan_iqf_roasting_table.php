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
        Schema::table('pembekuan_iqf_roasting', function (Blueprint $table) {
            $table->string('hasil_roasting_uuid')->nullable()->after('uuid');
            $table->string('input_roasting_uuid')->nullable()->after('hasil_roasting_uuid');
            $table->string('bahan_baku_roasting_uuid')->nullable()->after('input_roasting_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembekuan_iqf_roasting', function (Blueprint $table) {
            $table->dropColumn([
                'hasil_roasting_uuid',
                'input_roasting_uuid',
                'bahan_baku_roasting_uuid',
            ]);
        });
    }
};
