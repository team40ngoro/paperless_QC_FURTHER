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
        Schema::table('proses_aging', function (Blueprint $table) {
            $table->char('bahan_baku_tumbling_uuid', 36)->nullable()->after('proses_tumbling_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_aging', function (Blueprint $table) {
            $table->dropColumn('bahan_baku_tumbling_uuid');
        });
    }
};
