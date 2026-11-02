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
        Schema::table('std_fan', function (Blueprint $table) {
            $table->string('fan_3')->nullable()->after('std_fan_2');
            $table->string('fan_4')->nullable()->after('fan_3');
            $table->string('std_humadity')->nullable()->after('fan_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('std_fan', function (Blueprint $table) {
            $table->dropColumn(['fan_3', 'fan_4', 'std_humadity']);
        });
    }
};
