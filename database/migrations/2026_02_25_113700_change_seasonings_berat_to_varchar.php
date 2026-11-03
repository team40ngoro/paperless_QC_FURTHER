<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('seasonings', 'berat')) {
            DB::statement('ALTER TABLE `seasonings` MODIFY `berat` VARCHAR(255) NOT NULL');
        }
    }

    public function down()
    {
        // Reverting to integer can fail if existing values are non-numeric.
        // Keep as varchar.
    }
};
