<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('verif_peralatan_detail') && !Schema::hasColumn('verif_peralatan_detail', 'jam')) {
            Schema::table('verif_peralatan_detail', function (Blueprint $table) {
                $table->time('jam')->nullable()->after('id_area');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('verif_peralatan_detail') && Schema::hasColumn('verif_peralatan_detail', 'jam')) {
            Schema::table('verif_peralatan_detail', function (Blueprint $table) {
                $table->dropColumn('jam');
            });
        }
    }
};
