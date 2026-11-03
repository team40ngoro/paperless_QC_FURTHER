<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('penyimpanan_bahan', 'suhu_ruang')) {
            Schema::table('penyimpanan_bahan', function (Blueprint $table) {
                $table->dropColumn('suhu_ruang');
            });
        }
    }

    public function down(): void
    {
        // Adjust type if needed; previously looked like a string
        Schema::table('penyimpanan_bahan', function (Blueprint $table) {
            $table->string('suhu_ruang')->nullable();
        });
    }
};