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
        Schema::table('dokumentasi', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->after('id_shift');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dokumentasi', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn('user_id');
    });
    }
};
