<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pemeriksaan_rice_bites MODIFY premix JSON NULL');
    }

    public function down(): void
    {
        // Set existing NULLs to empty array before making NOT NULL
        DB::statement("UPDATE pemeriksaan_rice_bites SET premix = '[]' WHERE premix IS NULL");
        DB::statement('ALTER TABLE pemeriksaan_rice_bites MODIFY premix JSON NOT NULL');
    }
};
