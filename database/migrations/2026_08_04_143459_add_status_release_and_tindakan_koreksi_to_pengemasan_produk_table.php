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
        Schema::table('pengemasan_produk', function (Blueprint $table) {
            $table->string('status_release_produk')->nullable();
            $table->text('tindakan_koreksi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengemasan_produk', function (Blueprint $table) {
            $table->dropColumn(['status_release_produk', 'tindakan_koreksi']);
        });
    }
};
