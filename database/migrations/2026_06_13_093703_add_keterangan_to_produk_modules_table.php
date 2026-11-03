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
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('verifikasi');
        });

        Schema::table('produk_non_forming', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('produk_non_forming', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
