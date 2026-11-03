<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengemasan_plastik', function (Blueprint $table) {
            $table->string('line')->nullable()->after('kode_kemasan_plastik');
            $table->string('waktu_pengemasan')->nullable()->after('line');
        });
    }

    public function down()
    {
        Schema::table('pengemasan_plastik', function (Blueprint $table) {
            $table->dropColumn(['line', 'waktu_pengemasan']);
        });
    }
};
