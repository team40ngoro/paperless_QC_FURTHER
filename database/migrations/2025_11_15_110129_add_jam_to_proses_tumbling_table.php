<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->time('jam')->nullable()->after('tanggal')->comment('Waktu pelaksanaan proses tumbling');
        });
    }

    public function down(): void
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->dropColumn('jam');
        });
    }
};