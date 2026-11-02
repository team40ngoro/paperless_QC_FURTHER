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
        Schema::table('berat_produk_bag', function (Blueprint $table) {
            $table->dropForeign(['id_produk']);
                $table->foreign('id_pengemasan_produk')
                ->references('id')
                ->on('pengemasan_produk')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('berat_produk_bag', function (Blueprint $table) {
              $table->dropForeign(['id_produk']);

                $table->foreign('id_pengemasan_produk')
                ->references('id')
                ->on('jenis_produk')
                ->onDelete('cascade');
        });
    }
};
