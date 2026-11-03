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
            // Tambah field id_nama_barang sebagai foreign key
            $table->unsignedBigInteger('id_nama_barang')->after('tanggal');
            
            // Tambah foreign key constraint
            $table->foreign('id_nama_barang')->references('id')->on('data_barang')->onDelete('cascade');
            
            // Hapus field nama_barang lama
            $table->dropColumn('nama_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            // Drop foreign key constraint terlebih dahulu
            $table->dropForeign(['id_nama_barang']);
            
            // Drop field id_nama_barang
            $table->dropColumn('id_nama_barang');
            
            // Tambah kembali field nama_barang
            $table->string('nama_barang')->after('tanggal');
        });
    }
};
