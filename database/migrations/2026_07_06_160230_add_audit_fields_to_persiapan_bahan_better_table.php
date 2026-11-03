<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('uuid');
            $table->string('source_uuid')->nullable()->after('is_audit');
        });
    }

    public function down()
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            $table->dropColumn(['is_audit', 'source_uuid']);
        });
    }
};