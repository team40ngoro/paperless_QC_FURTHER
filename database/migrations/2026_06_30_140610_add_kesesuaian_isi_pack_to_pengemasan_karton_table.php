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
            $table->json('kesesuaian_isi_pack')->nullable()->after('aktual_jumlah_karton');
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
            $table->dropColumn('kesesuaian_isi_pack');
        });
    }
};
