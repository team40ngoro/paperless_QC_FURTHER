<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_cip', function (Blueprint $table) {
            $table->dropColumn('std_suhu');
        });
    }

    public function down(): void
    {
        Schema::table('master_cip', function (Blueprint $table) {
            $table->decimal('std_suhu', 8, 2)->nullable()->after('step_label')->comment('Standar suhu (°C)');
        });
    }
};
