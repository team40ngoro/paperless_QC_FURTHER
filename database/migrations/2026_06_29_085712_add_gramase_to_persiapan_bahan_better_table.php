<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom gramase (total gramase larutan batter) ke tabel persiapan_bahan_better.
     * Field gramase per baris bahan disimpan di dalam better_rows JSON.
     */
    public function up(): void
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            // Kolom gramase total (sum dari semua baris gramase)
            $table->decimal('gramase', 10, 2)->nullable()->after('berat_better')
                  ->comment('Total gramase larutan batter (gram)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->dropColumn('gramase');
        });
    }
};
