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
        Schema::table('pembuatan_predust', function (Blueprint $table) {
            $table->time('jam')->nullable()->after('tanggal')->comment('Waktu pelaksanaan pembuatan predust');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembuatan_predust', function (Blueprint $table) {
            $table->dropColumn('jam');
        });
    }
};