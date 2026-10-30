<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('std_berat_rheon')) {
            return;
        }

        if (!Schema::hasColumn('std_berat_rheon', 'std_adonan')) {
            Schema::table('std_berat_rheon', function (Blueprint $table) {
                $table->string('std_adonan')->default('');
            });
        }

        if (Schema::hasColumn('std_berat_rheon', 'std_rheon')) {
            DB::table('std_berat_rheon')->update([
                'std_adonan' => DB::raw('std_rheon'),
            ]);

            Schema::table('std_berat_rheon', function (Blueprint $table) {
                $table->dropColumn('std_rheon');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('std_berat_rheon')) {
            return;
        }

        if (!Schema::hasColumn('std_berat_rheon', 'std_rheon')) {
            Schema::table('std_berat_rheon', function (Blueprint $table) {
                $table->string('std_rheon')->default('');
            });
        }

        if (Schema::hasColumn('std_berat_rheon', 'std_adonan')) {
            DB::table('std_berat_rheon')->update([
                'std_rheon' => DB::raw('std_adonan'),
            ]);

            Schema::table('std_berat_rheon', function (Blueprint $table) {
                $table->dropColumn('std_adonan');
            });
        }
    }
};
