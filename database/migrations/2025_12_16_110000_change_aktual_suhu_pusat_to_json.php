<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change aktual_suhu_pusat from VARCHAR to JSON using raw SQL
        DB::statement('ALTER TABLE hasil_proses_roasting MODIFY aktual_suhu_pusat JSON NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to VARCHAR
        DB::statement('ALTER TABLE hasil_proses_roasting MODIFY aktual_suhu_pusat VARCHAR(255) NULL');
    }
};
