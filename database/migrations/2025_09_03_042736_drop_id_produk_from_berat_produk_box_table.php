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
             $table->dropForeign(['id_produk']);
        $table->dropColumn('id_produk');
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
            $table->foreignId('id_produk')->constrained('jenis_produk')->onDelete('cascade');
        });
    }
};
