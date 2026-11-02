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
          Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')->nullable()->after('user_id');
        $table->date('tanggal')->nullable()->after('kode_produksi_bahan_baku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_baku_tumbling', function (Blueprint $table) {
              $table->dropColumn(['shift_id', 'tanggal']);
        });
    }
};
