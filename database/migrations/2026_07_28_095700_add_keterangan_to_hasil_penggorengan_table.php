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
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
