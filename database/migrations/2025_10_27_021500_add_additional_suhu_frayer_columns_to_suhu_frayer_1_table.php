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
        Schema::table('suhu_frayer_1', function (Blueprint $table) {
            $table->string('suhu_frayer_3')->nullable()->after('suhu_frayer')->comment('Suhu Frayer 3');
            $table->string('suhu_frayer_4')->nullable()->after('suhu_frayer_3')->comment('Suhu Frayer 4');
            $table->string('suhu_frayer_5')->nullable()->after('suhu_frayer_4')->comment('Suhu Frayer 5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suhu_frayer_1', function (Blueprint $table) {
            $table->dropColumn([
                'suhu_frayer_3',
                'suhu_frayer_4', 
                'suhu_frayer_5'
            ]);
        });
    }
};
