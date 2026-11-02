<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuhuToBahanEmulsiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bahan_emulsi', function (Blueprint $table) {
            $table->string('suhu')->nullable()->after('berat_rm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_emulsi', function (Blueprint $table) {
            $table->dropColumn('suhu');
        });
    }
}
