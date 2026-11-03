<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->string('waktu_pemasakan')->nullable()->after('jam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->dropColumn('waktu_pemasakan');
        });
    }
};
