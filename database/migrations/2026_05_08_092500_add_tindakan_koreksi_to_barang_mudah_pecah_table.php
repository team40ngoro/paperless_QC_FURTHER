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
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_mudah_pecah', 'tindakan_koreksi')) {
                $table->text('tindakan_koreksi')->nullable()->after('temuan_ketidaksesuaian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->dropColumn('tindakan_koreksi');
        });
    }
};
