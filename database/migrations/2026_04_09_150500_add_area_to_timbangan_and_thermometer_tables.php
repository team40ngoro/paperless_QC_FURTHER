<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timbangan', function (Blueprint $table) {
            $table->string('area')->nullable()->after('jam');
        });

        Schema::table('thermometer', function (Blueprint $table) {
            $table->string('area')->nullable()->after('jam');
        });
    }

    public function down(): void
    {
        Schema::table('timbangan', function (Blueprint $table) {
            $table->dropColumn('area');
        });

        Schema::table('thermometer', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
