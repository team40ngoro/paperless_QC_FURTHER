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
            $table->string('kesesuaian_label_plastik')->nullable()->after('kode_kemasan_plastik');
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
            $table->dropColumn('kesesuaian_label_plastik');
        });
    }
};
