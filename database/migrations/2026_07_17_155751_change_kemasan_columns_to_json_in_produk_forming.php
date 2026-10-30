<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Change columns from INT to VARCHAR(255) to support JSON arrays
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY kemasan_plastik VARCHAR(255) NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY kemasan_karton VARCHAR(255) NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY labelisasi_plastik VARCHAR(255) NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY labelisasi_karton VARCHAR(255) NULL');
    }

    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY kemasan_plastik INT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY kemasan_karton INT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY labelisasi_plastik INT NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE produk_forming MODIFY labelisasi_karton INT NULL');
    }
};
