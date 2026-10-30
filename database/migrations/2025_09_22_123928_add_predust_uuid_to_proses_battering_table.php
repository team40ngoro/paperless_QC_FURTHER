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
        Schema::table('proses_battering', function (Blueprint $table) {
            $table->string('predust_uuid')->nullable()->after('penggorengan_uuid');
            $table->index('predust_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses_battering', function (Blueprint $table) {
            $table->dropIndex(['predust_uuid']);
            $table->dropColumn('predust_uuid');
        });
    }
};
