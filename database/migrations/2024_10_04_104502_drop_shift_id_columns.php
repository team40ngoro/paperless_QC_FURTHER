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
            'proses_battering',
            'proses_breader', 
            'proses_frayer',
            'pembekuan_iqf_penggorengan'
        ];

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $tableName) {
            // Check if table and column exist
            $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($tableExists)) {
                $columnExists = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE 'shift_id'");
                
                if (!empty($columnExists)) {
                    echo "Dropping shift_id from {$tableName}...\n";
                    
                    // First, try to find and drop any foreign key constraints
                    $constraints = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = '{$tableName}' 
                        AND COLUMN_NAME = 'shift_id' 
                        AND CONSTRAINT_NAME != 'PRIMARY'
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                    ");
                    
                    foreach ($constraints as $constraint) {
                        try {
                            DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                            echo "Dropped foreign key {$constraint->CONSTRAINT_NAME} from {$tableName}\n";
                        } catch (Exception $e) {
                            echo "Could not drop foreign key {$constraint->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                        }
                    }
                    
                    // Now drop the column
                    try {
                        DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `shift_id`");
                        echo "Successfully dropped shift_id from {$tableName}\n";
                    } catch (Exception $e) {
                        echo "Error dropping shift_id from {$tableName}: " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "Column shift_id does not exist in {$tableName}\n";
                }
            } else {
                echo "Table {$tableName} does not exist\n";
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        echo "Migration completed!\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'proses_battering',
            'proses_breader', 
            'proses_frayer',
            'pembekuan_iqf_penggorengan'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('shift_id')->nullable()->after('user_id');
            });
        }
    }
};
