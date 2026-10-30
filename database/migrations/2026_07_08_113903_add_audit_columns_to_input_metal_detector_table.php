<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('input_metal_detector', function (Blueprint $table) {
            if (!Schema::hasColumn('input_metal_detector', 'is_audit')) {
                $table->boolean('is_audit')->default(false);
            }
            if (!Schema::hasColumn('input_metal_detector', 'source_uuid')) {
                $table->uuid('source_uuid')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('input_metal_detector', function (Blueprint $table) {
            if (Schema::hasColumn('input_metal_detector', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
            if (Schema::hasColumn('input_metal_detector', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }
        });
    }
};
