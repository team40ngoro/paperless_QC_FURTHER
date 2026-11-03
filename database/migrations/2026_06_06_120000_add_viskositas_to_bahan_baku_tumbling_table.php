<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            if (!Schema::hasColumn('bahan_baku_tumbling', 'viskositas')) {
                $table->string('viskositas', 255)->nullable()->after('salinity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            if (Schema::hasColumn('bahan_baku_tumbling', 'viskositas')) {
                $table->dropColumn('viskositas');
            }
        });
    }
};
