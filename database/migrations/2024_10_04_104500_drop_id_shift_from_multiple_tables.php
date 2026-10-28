<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'pembekuan_iqf_penggorengan',
            'proses_battering',
            'proses_frayer',
            'frayer_2',
            'frayer_3',
            'frayer_4',
            'frayer_5',
            'proses_breader',
            'hasil_penggorengan'
        ];

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'id_shift')) {
                try {
                    // Drop the column directly using raw SQL
                    DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `id_shift`");
                } catch (Exception $e) {
                    // Log error but continue with other tables
                    \Log::warning("Could not drop id_shift from {$tableName}: " . $e->getMessage());
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'pembekuan_iqf_penggorengan',
            'proses_battering', 
            'proses_frayer',
            'frayer_2',
            'frayer_3',
            'frayer_4',
            'frayer_5',
            'proses_breader',
            'hasil_penggorengan'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('id_shift')->nullable()->after('user_id');
            });
        }
    }
};
