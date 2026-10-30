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
        if (Schema::hasTable('pemeriksaan_benda_asing') && !Schema::hasColumn('pemeriksaan_benda_asing', 'berat')) {
            Schema::table('pemeriksaan_benda_asing', function (Blueprint $table) {
                $table->decimal('berat', 8, 2)->nullable()->after('id_produk');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pemeriksaan_benda_asing') && Schema::hasColumn('pemeriksaan_benda_asing', 'berat')) {
            Schema::table('pemeriksaan_benda_asing', function (Blueprint $table) {
                $table->dropColumn('berat');
            });
        }
    }
};
