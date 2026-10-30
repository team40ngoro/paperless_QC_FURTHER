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
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->string('frayer2_uuid')->nullable()->after('frayer_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->dropColumn('frayer2_uuid');
        });
    }
};
