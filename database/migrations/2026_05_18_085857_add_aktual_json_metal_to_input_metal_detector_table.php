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
        Schema::table('input_metal_detector', function (Blueprint $table) {
            $table->json('aktual_json_metal')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('input_metal_detector', function (Blueprint $table) {
            $table->dropColumn('aktual_json_metal');
        });
    }
};
