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
        Schema::table('total_pemakaian_emulsi', function (Blueprint $table) {
            $table->string('total_pemakaian', 255)->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('total_pemakaian_emulsi', function (Blueprint $table) {
            $table->dropColumn('total_pemakaian');
        });
    }
};