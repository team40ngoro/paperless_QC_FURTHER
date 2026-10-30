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
        Schema::table('pengemasan_produk', function (Blueprint $table) {
            $table->boolean('is_kombinasi')->default(0)->after('kode_produksi');
            $table->unsignedBigInteger('id_produk_2')->nullable()->after('is_kombinasi');
            $table->string('kode_produksi_2')->nullable()->after('id_produk_2');
            $table->string('berat_produk_2')->nullable()->after('kode_produksi_2');
            $table->date('tanggal_expired_2')->nullable()->after('berat_produk_2');

            $table->foreign('id_produk_2')->references('id')->on('jenis_produk')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengemasan_produk', function (Blueprint $table) {
            $table->dropForeign(['id_produk_2']);
            $table->dropColumn([
                'is_kombinasi',
                'id_produk_2',
                'kode_produksi_2',
                'berat_produk_2',
                'tanggal_expired_2',
            ]);
        });
    }
};
