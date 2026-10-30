<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('kontrol_sanitasi', 'suhu_air')) {
            DB::statement("ALTER TABLE kontrol_sanitasi MODIFY suhu_air VARCHAR(255)");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kontrol_sanitasi', 'suhu_air')) {
            DB::statement("ALTER TABLE kontrol_sanitasi MODIFY suhu_air INT");
        }
    }
};
