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
            // Add group_uuid field after uuid
            $table->uuid('group_uuid')->nullable()->after('uuid');
            
            // Add index for better performance
            $table->index('group_uuid');
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
            // Drop index and column
            $table->dropIndex(['group_uuid']);
            $table->dropColumn('group_uuid');
        });
    }
};
