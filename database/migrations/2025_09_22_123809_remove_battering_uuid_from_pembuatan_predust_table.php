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
        Schema::table('pembuatan_predust', function (Blueprint $table) {
            $table->dropIndex(['battering_uuid']);
            $table->dropColumn('battering_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembuatan_predust', function (Blueprint $table) {
            $table->string('battering_uuid')->nullable()->after('penggorengan_uuid');
            $table->index('battering_uuid');
        });
    }
};
