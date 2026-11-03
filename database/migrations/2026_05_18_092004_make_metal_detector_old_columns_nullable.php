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
        DB::statement("ALTER TABLE input_metal_detector MODIFY fe_depan_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY fe_tengah_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY fe_belakang_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY non_fe_depan_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY non_fe_tengah_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY non_fe_belakang_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY sus_depan_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY sus_tengah_aktual VARCHAR(255) NULL");
        DB::statement("ALTER TABLE input_metal_detector MODIFY sus_belakang_aktual VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
