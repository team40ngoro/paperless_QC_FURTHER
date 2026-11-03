<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proses_twahing', function (Blueprint $table) {
            if (!Schema::hasColumn('proses_twahing', 'jam')) {
                $table->time('jam')->nullable()->after('tanggal');
            }
        });
    }

    public function down()
    {
        Schema::table('proses_twahing', function (Blueprint $table) {
            if (Schema::hasColumn('proses_twahing', 'jam')) {
                $table->dropColumn('jam');
            }
        });
    }
};
