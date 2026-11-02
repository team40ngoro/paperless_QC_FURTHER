<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('std_berat_rheon')) {
            return;
        }

        Schema::table('std_berat_rheon', function (Blueprint $table) {
            if (!Schema::hasColumn('std_berat_rheon', 'std_filler')) {
                $table->string('std_filler')->nullable();
            }
            if (!Schema::hasColumn('std_berat_rheon', 'std_after_forming')) {
                $table->string('std_after_forming')->nullable();
            }
            if (!Schema::hasColumn('std_berat_rheon', 'std_after_frying')) {
                $table->string('std_after_frying')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('std_berat_rheon')) {
            return;
        }

        Schema::table('std_berat_rheon', function (Blueprint $table) {
            if (Schema::hasColumn('std_berat_rheon', 'std_filler')) {
                $table->dropColumn('std_filler');
            }
            if (Schema::hasColumn('std_berat_rheon', 'std_after_forming')) {
                $table->dropColumn('std_after_forming');
            }
            if (Schema::hasColumn('std_berat_rheon', 'std_after_frying')) {
                $table->dropColumn('std_after_frying');
            }
        });
    }
};
