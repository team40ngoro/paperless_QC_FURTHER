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
        Schema::table('penggorengan', function (Blueprint $table) {
            $table->string('berat_produk')->nullable()->after('kode_produksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggorengan', function (Blueprint $table) {
            $table->dropColumn('berat_produk');
        });
    }
};
