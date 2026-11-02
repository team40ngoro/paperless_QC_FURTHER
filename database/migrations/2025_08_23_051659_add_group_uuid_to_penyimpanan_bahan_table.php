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
        Schema::table('penyimpanan_bahan', function (Blueprint $table) {
            $table->uuid('group_uuid')->nullable()->index()->after('uuid');
        });
    }

    public function down()
    {
        Schema::table('penyimpanan_bahan', function (Blueprint $table) {
            $table->dropColumn('group_uuid');
        });
    }
};
