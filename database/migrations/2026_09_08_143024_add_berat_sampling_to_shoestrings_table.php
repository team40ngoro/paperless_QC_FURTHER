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
        if (!Schema::hasColumn('shoestrings', 'berat_sampling')) {
            Schema::table('shoestrings', function (Blueprint $table) {
                $table->string('berat_sampling')->nullable()->after('best_before');
            });
        }
        if (Schema::hasTable('shoestring_logs') && !Schema::hasColumn('shoestring_logs', 'berat_sampling')) {
            Schema::table('shoestring_logs', function (Blueprint $table) {
                $table->string('berat_sampling')->nullable()->after('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('shoestrings', 'berat_sampling')) {
            Schema::table('shoestrings', function (Blueprint $table) {
                $table->dropColumn('berat_sampling');
            });
        }
        if (Schema::hasTable('shoestring_logs') && Schema::hasColumn('shoestring_logs', 'berat_sampling')) {
            Schema::table('shoestring_logs', function (Blueprint $table) {
                $table->dropColumn('berat_sampling');
            });
        }
    }
};
