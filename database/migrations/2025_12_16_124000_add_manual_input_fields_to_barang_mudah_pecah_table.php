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
            $table->boolean('is_manual')->default(false)->after('id_sub_area');
            $table->string('nama_barang_manual')->nullable()->after('is_manual');
            $table->string('nama_karyawan')->nullable()->after('nama_barang_manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->dropColumn(['is_manual', 'nama_barang_manual', 'nama_karyawan']);
        });
    }
};
