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
        Schema::table('master_standar_audits', function (Blueprint $table) {
            $table->unsignedBigInteger('id_plan')->nullable()->after('uuid');
            $table->index(['id_plan', 'module_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_standar_audits', function (Blueprint $table) {
            $table->dropIndex(['id_plan', 'module_name']);
            $table->dropColumn('id_plan');
        });
    }
};