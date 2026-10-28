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
        Schema::table('reboxes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_plan')->nullable()->after('uuid');
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reboxes', function (Blueprint $table) {
            $table->dropForeign(['id_plan']);
            $table->dropColumn('id_plan');
        });
    }
};
