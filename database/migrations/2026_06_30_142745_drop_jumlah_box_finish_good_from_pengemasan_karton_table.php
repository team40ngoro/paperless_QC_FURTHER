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
            $table->dropColumn('jumlah_box_finish_good');
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
            $table->string('jumlah_box_finish_good')->nullable();
        });
    }
};
