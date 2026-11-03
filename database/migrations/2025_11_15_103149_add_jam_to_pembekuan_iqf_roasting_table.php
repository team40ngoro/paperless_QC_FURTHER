<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembekuan_iqf_roasting', function (Blueprint $table) {
            $table->time('jam')->nullable()->after('tanggal')->comment('Waktu pelaksanaan pembekuan IQF roasting');
        });
    }

    public function down(): void
    {
        Schema::table('pembekuan_iqf_roasting', function (Blueprint $table) {
            $table->dropColumn('jam');
        });
    }
};