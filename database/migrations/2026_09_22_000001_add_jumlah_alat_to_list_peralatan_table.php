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
        Schema::table('list_peralatan', function (Blueprint $table) {
            $table->integer('jumlah_alat')->nullable()->default(1)->after('nama_alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_peralatan', function (Blueprint $table) {
            $table->dropColumn('jumlah_alat');
        });
    }
};
