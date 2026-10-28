<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE release_produk_finish_goods MODIFY suhu_produk VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE release_produk_finish_goods MODIFY suhu_produk DECIMAL(8,2)");
    }
};
