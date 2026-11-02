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
        Schema::table('data_defect', function (Blueprint $table) {
            $table->string('spec_defect')->nullable()->after('jenis_defect');
            $table->index('spec_defect');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_defect', function (Blueprint $table) {
            $table->dropIndex(['spec_defect']);
            $table->dropColumn('spec_defect');
        });
    }
};
