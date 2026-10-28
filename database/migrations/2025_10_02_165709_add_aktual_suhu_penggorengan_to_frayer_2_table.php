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
        Schema::table('frayer_2', function (Blueprint $table) {
            $table->string('aktual_suhu_penggorengan')->nullable()->after('aktual_penggorengan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frayer_2', function (Blueprint $table) {
            $table->dropColumn('aktual_suhu_penggorengan');
        });
    }
};
