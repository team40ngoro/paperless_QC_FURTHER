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
        Schema::table('proses_breader', function (Blueprint $table) {
            $table->string('battering_uuid')->nullable()->after('uuid');
            $table->string('predust_uuid')->nullable()->after('battering_uuid');
            $table->string('penggorengan_uuid')->nullable()->after('predust_uuid');
            
            $table->index('battering_uuid');
            $table->index('predust_uuid');
            $table->index('penggorengan_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses_breader', function (Blueprint $table) {
            $table->dropIndex(['battering_uuid']);
            $table->dropIndex(['predust_uuid']);
            $table->dropIndex(['penggorengan_uuid']);
            
            $table->dropColumn(['battering_uuid', 'predust_uuid', 'penggorengan_uuid']);
        });
    }
};
