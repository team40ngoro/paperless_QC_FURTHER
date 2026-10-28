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
            $table->string('frayer_uuid')->nullable()->after('uuid');
            $table->index('frayer_uuid');
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
            $table->dropIndex(['frayer_uuid']);
            $table->dropColumn('frayer_uuid');
        });
    }
};
