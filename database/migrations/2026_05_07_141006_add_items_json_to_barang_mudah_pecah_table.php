<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_mudah_pecah', 'items_json')) {
                $table->json('items_json')->nullable()->after('jam');
            }
        });

        // Use raw SQL to avoid Doctrine DBAL 4 incompatibility in Laravel 9
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY id_area BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY id_nama_barang BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY jumlah INT NULL');
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY kondisi VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->dropColumn('items_json');
        });

        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY id_area BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY id_nama_barang BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY jumlah INT NOT NULL');
        DB::statement('ALTER TABLE barang_mudah_pecah MODIFY kondisi VARCHAR(255) NOT NULL');
    }
};
