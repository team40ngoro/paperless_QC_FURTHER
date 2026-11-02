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
      Schema::table('shoestrings', function (Blueprint $table) {
        $table->unsignedBigInteger('shift_id')->nullable()->after('kode_produksi');
        $table->date('tanggal')->nullable()->after('best_before');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('shoestrings', function (Blueprint $table) {
        $table->dropColumn(['shift_id', 'tanggal']);
    });
    }
};
