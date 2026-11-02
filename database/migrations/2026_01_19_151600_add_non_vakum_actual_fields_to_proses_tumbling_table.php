<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->string('aktual_drum_on_non_vakum')->nullable()->after('aktual_vakum');
            $table->string('aktual_drum_off_non_vakum')->nullable()->after('aktual_drum_on_non_vakum');
            $table->string('aktual_speed_non_vakum')->nullable()->after('aktual_drum_off_non_vakum');
            $table->string('aktual_total_waktu_non_vakum')->nullable()->after('aktual_speed_non_vakum');
            $table->string('aktual_tekanan_non_vakum')->nullable()->after('aktual_total_waktu_non_vakum');
        });
    }

    public function down(): void
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->dropColumn([
                'aktual_drum_on_non_vakum',
                'aktual_drum_off_non_vakum',
                'aktual_speed_non_vakum',
                'aktual_total_waktu_non_vakum',
                'aktual_tekanan_non_vakum',
            ]);
        });
    }
};
