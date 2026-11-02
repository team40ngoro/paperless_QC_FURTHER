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
    public function up(): void
    {
        Schema::table('timbangan', function (Blueprint $table) {
            $table->json('entries')->nullable()->after('jam');
        });

        Schema::table('thermometer', function (Blueprint $table) {
            $table->json('entries')->nullable()->after('jam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timbangan', function (Blueprint $table) {
            $table->dropColumn('entries');
        });

        Schema::table('thermometer', function (Blueprint $table) {
            $table->dropColumn('entries');
        });
    }
};
