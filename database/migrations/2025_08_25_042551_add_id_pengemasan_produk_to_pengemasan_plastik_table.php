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
        Schema::table('pengemasan_plastik', function (Blueprint $table) {
             $table->unsignedBigInteger('id_pengemasan_produk')->after('user_id');
        $table->foreign('id_pengemasan_produk')->references('id')->on('pengemasan_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengemasan_plastik', function (Blueprint $table) {
              $table->dropForeign(['id_pengemasan_produk']);
        $table->dropColumn('id_pengemasan_produk');
        });
    }
};
