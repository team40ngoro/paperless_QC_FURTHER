<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_produk', function (Blueprint $table) {
            $table->json('id_produk_rework')->nullable()->after('status_bahan');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_produk', function (Blueprint $table) {
            $table->dropColumn('id_produk_rework');
        });
    }
};
