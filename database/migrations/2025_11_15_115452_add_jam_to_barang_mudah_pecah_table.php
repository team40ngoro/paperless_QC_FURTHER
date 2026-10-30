<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->time('jam')->nullable()->after('tanggal')->comment('Waktu pelaksanaan barang mudah pecah');
        });
    }

    public function down(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->dropColumn('jam');
        });
    }
};