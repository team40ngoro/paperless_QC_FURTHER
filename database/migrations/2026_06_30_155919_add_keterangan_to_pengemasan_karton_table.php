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
        if (!Schema::hasColumn('pengemasan_karton', 'keterangan')) {
            Schema::table('pengemasan_karton', function (Blueprint $table) {
                $table->text('keterangan')->nullable()->after('kesesuaian_isi_pack');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('pengemasan_karton', 'keterangan')) {
            Schema::table('pengemasan_karton', function (Blueprint $table) {
                $table->dropColumn('keterangan');
            });
        }
    }
};
