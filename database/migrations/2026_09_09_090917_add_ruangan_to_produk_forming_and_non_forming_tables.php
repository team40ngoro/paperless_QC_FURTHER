<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->json('ruangan')->nullable()->after('mesin_peralatan');
        });

        Schema::table('produk_non_forming', function (Blueprint $table) {
            $table->json('ruangan')->nullable()->after('mesin_peralatan');
        });
    }

    public function down(): void
    {
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->dropColumn('ruangan');
        });

        Schema::table('produk_non_forming', function (Blueprint $table) {
            $table->dropColumn('ruangan');
        });
    }
};
