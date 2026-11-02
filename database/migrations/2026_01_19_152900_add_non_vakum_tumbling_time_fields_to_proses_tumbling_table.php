<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->string('waktu_mulai_tumbling_non_vakum')->nullable()->after('waktu_selesai_tumbling');
            $table->string('waktu_selesai_tumbling_non_vakum')->nullable()->after('waktu_mulai_tumbling_non_vakum');
        });
    }

    public function down(): void
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_mulai_tumbling_non_vakum',
                'waktu_selesai_tumbling_non_vakum',
            ]);
        });
    }
};
