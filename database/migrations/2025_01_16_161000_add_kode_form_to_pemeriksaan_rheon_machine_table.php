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
        Schema::table('pemeriksaan_rheon_machine', function (Blueprint $table) {
            $table->string('kode_form', 50)->nullable()->after('uuid');
            $table->index('kode_form');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_rheon_machine', function (Blueprint $table) {
            $table->dropIndex(['kode_form']);
            $table->dropColumn('kode_form');
        });
    }
};
