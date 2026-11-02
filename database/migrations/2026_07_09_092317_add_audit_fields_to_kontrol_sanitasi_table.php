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
        Schema::table('kontrol_sanitasi', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('id');
            $table->uuid('source_uuid')->nullable()->after('is_audit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontrol_sanitasi', function (Blueprint $table) {
            $table->dropColumn('is_audit');
            $table->dropColumn('source_uuid');
        });
    }
};
