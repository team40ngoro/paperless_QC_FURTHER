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
        Schema::table('pengemasan_karton', function (Blueprint $table) {
              $table->dropForeign(['id_produk']);
            $table->dropColumn('id_produk');

               // Tambahkan kolom baru
            $table->unsignedBigInteger('id_berat_produk_box')->nullable()->after('user_id');
            $table->unsignedBigInteger('id_berat_produk_bag')->nullable()->after('id_berat_produk_box');
            $table->unsignedBigInteger('id_pengemasan_plastik')->nullable()->after('id_berat_produk_bag');
            $table->unsignedBigInteger('id_pengemasan_produk')->nullable()->after('id_pengemasan_plastik');

            // Tambahkan foreign key baru
            $table->foreign('id_berat_produk_box')->references('id')->on('berat_produk_box')->onDelete('cascade');;
            $table->foreign('id_berat_produk_bag')->references('id')->on('berat_produk_bag')->onDelete('cascade');;
            $table->foreign('id_pengemasan_plastik')->references('id')->on('pengemasan_plastik')->onDelete('cascade');;
            $table->foreign('id_pengemasan_produk')->references('id')->on('pengemasan_produk')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengemasan_karton', function (Blueprint $table) {
               // Drop foreign keys
            $table->dropForeign(['id_berat_produk_box']);
            $table->dropForeign(['id_berat_produk_bag']);
            $table->dropForeign(['id_pengemasan_plastik']);
            $table->dropForeign(['id_pengemasan_produk']);

            // Drop columns
            $table->dropColumn(['id_berat_produk_box', 'id_berat_produk_bag', 'id_pengemasan_plastik', 'id_pengemasan_produk']);

            // Tambahkan kembali kolom id_produk
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
        });
    }
};
