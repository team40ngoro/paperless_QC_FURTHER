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
        Schema::table('pembuatan_sample', function (Blueprint $table) {
            $table->text('items_json')->nullable()->after('kode_form');
        });
    }

    public function down()
    {
        Schema::table('pembuatan_sample', function (Blueprint $table) {
            $table->dropColumn('items_json');
        });
    }
};
