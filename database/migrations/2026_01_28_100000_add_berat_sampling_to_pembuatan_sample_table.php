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
            $table->decimal('berat_sampling', 8, 2)->nullable()->after('berat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembuatan_sample', function (Blueprint $table) {
            $table->dropColumn('berat_sampling');
        });
    }
};
