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
            $table->boolean('is_audit')->default(false)->after('kode_form');
            $table->uuid('source_uuid')->nullable()->after('is_audit');
        });
    }

    public function down()
    {
        Schema::table('proses_aging', function (Blueprint $table) {
            $table->dropColumn(['is_audit', 'source_uuid']);
        });
    }
};
