<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change aktual_suhu_pusat from decimal to string using raw SQL
        DB::statement('ALTER TABLE hasil_proses_roasting MODIFY aktual_suhu_pusat VARCHAR(255) NULL');
        
        // Create a temporary column for sensori
        DB::statement('ALTER TABLE hasil_proses_roasting ADD COLUMN sensori_temp JSON NULL');
        
        // Copy data from sensori to sensori_temp, converting to valid JSON
        DB::statement("UPDATE hasil_proses_roasting SET sensori_temp = JSON_OBJECT() WHERE sensori IS NULL OR sensori = ''");
        DB::statement("UPDATE hasil_proses_roasting SET sensori_temp = JSON_OBJECT('value', sensori) WHERE sensori IS NOT NULL AND sensori != ''");
        
        // Drop the old sensori column
        DB::statement('ALTER TABLE hasil_proses_roasting DROP COLUMN sensori');
        
        // Rename the temporary column to sensori
        DB::statement('ALTER TABLE hasil_proses_roasting RENAME COLUMN sensori_temp TO sensori');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert aktual_suhu_pusat back to decimal
        DB::statement('ALTER TABLE hasil_proses_roasting MODIFY aktual_suhu_pusat DECIMAL(5, 2) NULL');
        
        // Create a temporary column for sensori
        DB::statement('ALTER TABLE hasil_proses_roasting ADD COLUMN sensori_temp TEXT NULL');
        
        // Copy data back from sensori to sensori_temp
        DB::statement("UPDATE hasil_proses_roasting SET sensori_temp = JSON_EXTRACT(sensori, '$.value') WHERE JSON_EXTRACT(sensori, '$.value') IS NOT NULL");
        
        // Drop the old sensori column
        DB::statement('ALTER TABLE hasil_proses_roasting DROP COLUMN sensori');
        
        // Rename the temporary column to sensori
        DB::statement('ALTER TABLE hasil_proses_roasting RENAME COLUMN sensori_temp TO sensori');
    }
};
