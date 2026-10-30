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
        $table->unsignedBigInteger('id_pengemasan_plastik')->nullable()->after('id_pengemasan_produk');
    });
       Schema::table('berat_produk_bag', function (Blueprint $table) {
        $table->foreign('id_pengemasan_plastik')
            ->references('id')
            ->on('pengemasan_plastik')
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
        $table->dropForeign(['id_pengemasan_plastik']);
        $table->dropColumn('id_pengemasan_plastik');
    });
    }
};
