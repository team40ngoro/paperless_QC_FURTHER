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
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->json('better_rows')->nullable()->after('sensori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->dropColumn('better_rows');
        });
    }
};
