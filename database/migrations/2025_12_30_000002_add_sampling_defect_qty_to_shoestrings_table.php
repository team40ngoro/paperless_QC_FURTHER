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
            $table->text('sampling_defect_qty')->nullable()->after('sampling_defect');
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
            $table->dropColumn('sampling_defect_qty');
        });
    }
};
