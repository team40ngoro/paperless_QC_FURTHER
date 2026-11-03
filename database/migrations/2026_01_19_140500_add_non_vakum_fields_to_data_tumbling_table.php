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
        Schema::table('data_tumbling', function (Blueprint $table) {
            $table->string('drum_on_non_vakum')->nullable()->after('tekanan_vakum');
            $table->string('drum_off_non_vakum')->nullable()->after('drum_on_non_vakum');
            $table->string('drum_speed_non_vakum')->nullable()->after('drum_off_non_vakum');
            $table->string('total_waktu_non_vakum')->nullable()->after('drum_speed_non_vakum');
            $table->string('tekanan_non_vakum')->nullable()->after('total_waktu_non_vakum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_tumbling', function (Blueprint $table) {
            $table->dropColumn([
                'drum_on_non_vakum',
                'drum_off_non_vakum',
                'drum_speed_non_vakum',
                'total_waktu_non_vakum',
                'tekanan_non_vakum',
            ]);
        });
    }
};
