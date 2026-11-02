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
        Schema::table('berat_produk_box', function (Blueprint $table) {
        $table->unsignedBigInteger('id_pengemasan_plastik')->nullable()->after('id');
        $table->unsignedBigInteger('id_pengemasan_produk')->nullable()->after('id_pengemasan_plastik');
        $table->unsignedBigInteger('id_berat_produk_bag')->nullable()->after('id_pengemasan_produk');

        $table->foreign('id_pengemasan_plastik')->references('id')->on('pengemasan_plastik')->onDelete('cascade');
        $table->foreign('id_pengemasan_produk')->references('id')->on('pengemasan_produk')->onDelete('cascade');
        $table->foreign('id_berat_produk_bag')->references('id')->on('berat_produk_bag')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('berat_produk_box', function (Blueprint $table) {
              $table->dropForeign(['id_pengemasan_plastik']);
        $table->dropColumn('id_pengemasan_plastik');

        $table->dropForeign(['id_pengemasan_produk']);
        $table->dropColumn('id_pengemasan_produk');

        $table->dropForeign(['id_berat_produk_bag']);
        $table->dropColumn('id_berat_produk_bag');
        });
    }
};
