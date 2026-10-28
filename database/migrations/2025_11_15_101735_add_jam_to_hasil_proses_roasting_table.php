<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_proses_roasting', function (Blueprint $table) {
            $table->time('jam')->nullable()->after('tanggal')->comment('Waktu pelaksanaan hasil proses roasting');
        });
    }

    public function down(): void
    {
        Schema::table('hasil_proses_roasting', function (Blueprint $table) {
            $table->dropColumn('jam');
        });
    }
};