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
        Schema::table('area_proses', function (Blueprint $table) {
            if (!Schema::hasColumn('area_proses', 'is_audit')) {
                $table->boolean('is_audit')->default(false);
            }
            if (!Schema::hasColumn('area_proses', 'source_uuid')) {
                $table->uuid('source_uuid')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('area_proses', function (Blueprint $table) {
            if (Schema::hasColumn('area_proses', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
            if (Schema::hasColumn('area_proses', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }
        });
    }
};
