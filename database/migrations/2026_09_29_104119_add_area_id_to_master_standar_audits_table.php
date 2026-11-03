<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('master_standar_audits', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable()->after('module_name');
        });
    }

    public function down()
    {
        Schema::table('master_standar_audits', function (Blueprint $table) {
            $table->dropColumn('area_id');
        });
    }
};
