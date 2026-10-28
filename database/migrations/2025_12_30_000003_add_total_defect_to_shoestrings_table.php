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
        Schema::table('shoestrings', function (Blueprint $table) {
            $table->string('total_defect')->nullable()->after('sampling_defect_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoestrings', function (Blueprint $table) {
            $table->dropColumn('total_defect');
        });
    }
};
