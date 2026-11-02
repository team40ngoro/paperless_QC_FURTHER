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
        Schema::table('timbangan', function (Blueprint $table) {
            if (!Schema::hasColumn('timbangan', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->after('uuid');
            }
            if (!Schema::hasColumn('timbangan', 'source_uuid')) {
                $table->uuid('source_uuid')->nullable()->after('is_audit');
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
        Schema::table('timbangan', function (Blueprint $table) {
            if (Schema::hasColumn('timbangan', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
            if (Schema::hasColumn('timbangan', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }
        });
    }
};
