<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dokumentasi', function (Blueprint $table) {
              // Hapus foreign key dan kolom id_produk
            $table->dropForeign(['id_produk']);
            $table->dropColumn('id_produk');

            // Tambahkan kolom baru
            $table->unsignedBigInteger('id_pengemasan_karton')->nullable()->after('id_plan');
            $table->unsignedBigInteger('id_berat_produk_box')->nullable()->after('id_pengemasan_karton');
            $table->unsignedBigInteger('id_berat_produk_bag')->nullable()->after('id_berat_produk_box');
            $table->unsignedBigInteger('id_pengemasan_plastik')->nullable()->after('id_berat_produk_bag');
            $table->unsignedBigInteger('id_pengemasan_produk')->nullable()->after('id_pengemasan_plastik');

            // Tambahkan foreign key baru (pastikan tabel referensi sudah ada)
            $table->foreign('id_pengemasan_karton')->references('id')->on('pengemasan_karton')->onDelete('set null');
            $table->foreign('id_berat_produk_box')->references('id')->on('berat_produk_box')->onDelete('set null');
            $table->foreign('id_berat_produk_bag')->references('id')->on('berat_produk_bag')->onDelete('set null');
            $table->foreign('id_pengemasan_plastik')->references('id')->on('pengemasan_plastik')->onDelete('set null');
            $table->foreign('id_pengemasan_produk')->references('id')->on('pengemasan_produk')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dokumentasi', function (Blueprint $table) {
             
            $table->dropForeign(['id_pengemasan_karton']);
            $table->dropForeign(['id_berat_produk_box']);
            $table->dropForeign(['id_berat_produk_bag']);
            $table->dropForeign(['id_pengemasan_plastik']);
            $table->dropForeign(['id_pengemasan_produk']);

        
            $table->dropColumn([
                'id_pengemasan_karton',
                'id_berat_produk_box',
                'id_berat_produk_bag',
                'id_pengemasan_plastik',
                'id_pengemasan_produk'
            ]);

            // Tambahkan kembali kolom id_produk
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
        });
    }
};
