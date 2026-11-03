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
        Schema::table('produk_yum', function (Blueprint $table) {
            $table->string('kode_form', 50)->nullable()->after('uuid');
            $table->index('kode_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_yum', function (Blueprint $table) {
            $table->dropIndex(['kode_form']);
            $table->dropColumn('kode_form');
        });
    }
};
