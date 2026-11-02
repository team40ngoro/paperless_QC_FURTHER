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
        Schema::table('ketidaksesuaian_plastik', function (Blueprint $table) {
            $table->string('jumlah_ketidaksesuaian', 255)->nullable()->after('nama_plastik');
        });
    }

    public function down()
    {
        Schema::table('ketidaksesuaian_plastik', function (Blueprint $table) {
            $table->dropColumn('jumlah_ketidaksesuaian');
        });
    }
};
