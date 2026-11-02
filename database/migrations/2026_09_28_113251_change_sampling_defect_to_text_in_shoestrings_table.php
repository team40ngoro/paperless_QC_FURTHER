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
             DB::statement('ALTER TABLE shoestrings MODIFY sampling_defect TEXT NULL');
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
          DB::statement('ALTER TABLE shoestrings MODIFY sampling_defect VARCHAR(255) NULL');
        });
    }
};
