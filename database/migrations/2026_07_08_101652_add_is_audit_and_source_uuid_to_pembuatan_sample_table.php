<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembuatan_sample', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('kode_form');
            $table->uuid('source_uuid')->nullable()->after('is_audit');
        });
    }

    public function down()
    {
        Schema::table('pembuatan_sample', function (Blueprint $table) {
            $table->dropColumn(['is_audit', 'source_uuid']);
        });
    }
};
