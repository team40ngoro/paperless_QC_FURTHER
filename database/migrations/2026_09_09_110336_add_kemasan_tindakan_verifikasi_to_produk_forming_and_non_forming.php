<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            'kemasan_plastik_tindakan',
            'kemasan_plastik_verifikasi',
            'kemasan_karton_tindakan',
            'kemasan_karton_verifikasi',
            'labelisasi_plastik_tindakan',
            'labelisasi_plastik_verifikasi',
            'labelisasi_karton_tindakan',
            'labelisasi_karton_verifikasi',
        ];

        Schema::table('produk_forming', function (Blueprint $table) use ($fields) {
            foreach ($fields as $field) {
                $table->string($field)->nullable()->after('labelisasi_karton');
            }
        });

        Schema::table('produk_non_forming', function (Blueprint $table) use ($fields) {
            foreach ($fields as $field) {
                $table->string($field)->nullable()->after('labelisasi_karton');
            }
        });
    }

    public function down(): void
    {
        $fields = [
            'kemasan_plastik_tindakan', 'kemasan_plastik_verifikasi',
            'kemasan_karton_tindakan', 'kemasan_karton_verifikasi',
            'labelisasi_plastik_tindakan', 'labelisasi_plastik_verifikasi',
            'labelisasi_karton_tindakan', 'labelisasi_karton_verifikasi',
        ];

        Schema::table('produk_forming', function (Blueprint $table) use ($fields) {
            $table->dropColumn($fields);
        });

        Schema::table('produk_non_forming', function (Blueprint $table) use ($fields) {
            $table->dropColumn($fields);
        });
    }
};
