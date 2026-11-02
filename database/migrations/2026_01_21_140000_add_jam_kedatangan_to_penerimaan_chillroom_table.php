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
        if (Schema::hasTable('penerimaan_chillroom') && !Schema::hasColumn('penerimaan_chillroom', 'jam_kedatangan')) {
            Schema::table('penerimaan_chillroom', function (Blueprint $table) {
                $table->time('jam_kedatangan')->nullable()->after('shift_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('penerimaan_chillroom') && Schema::hasColumn('penerimaan_chillroom', 'jam_kedatangan')) {
            Schema::table('penerimaan_chillroom', function (Blueprint $table) {
                $table->dropColumn('jam_kedatangan');
            });
        }
    }
};
