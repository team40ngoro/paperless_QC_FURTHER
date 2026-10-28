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
        // 1. Rename column
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE berat_produk_bag CHANGE berat_aktual_1 berat_aktual TEXT');

        // 3. Migrate data
        $records = \Illuminate\Support\Facades\DB::table('berat_produk_bag')->get();
        foreach ($records as $record) {
            $array = [];
            if (!empty($record->berat_aktual)) {
                $array[] = $record->berat_aktual;
            }
            if (!empty($record->berat_aktual_2)) {
                $array[] = $record->berat_aktual_2;
            }
            if (!empty($record->berat_aktual_3)) {
                $array[] = $record->berat_aktual_3;
            }
            
            \Illuminate\Support\Facades\DB::table('berat_produk_bag')
                ->where('id', $record->id)
                ->update([
                    'berat_aktual' => json_encode($array)
                ]);
        }

        // 4. Drop columns
        Schema::table('berat_produk_bag', function (Blueprint $table) {
            $table->dropColumn(['berat_aktual_2', 'berat_aktual_3']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('berat_produk_bag', function (Blueprint $table) {
            $table->decimal('berat_aktual_2', 8, 2)->nullable();
            $table->decimal('berat_aktual_3', 8, 2)->nullable();
        });

        // Try to revert data if possible
        $records = \Illuminate\Support\Facades\DB::table('berat_produk_bag')->get();
        foreach ($records as $record) {
            $decoded = json_decode($record->berat_aktual, true);
            $val1 = null;
            $val2 = null;
            $val3 = null;
            
            if (is_array($decoded)) {
                $val1 = $decoded[0] ?? null;
                $val2 = $decoded[1] ?? null;
                $val3 = $decoded[2] ?? null;
            } else {
                $val1 = $record->berat_aktual;
            }

            \Illuminate\Support\Facades\DB::table('berat_produk_bag')
                ->where('id', $record->id)
                ->update([
                    'berat_aktual' => $val1,
                    'berat_aktual_2' => $val2,
                    'berat_aktual_3' => $val3
                ]);
        }

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE berat_produk_bag CHANGE berat_aktual berat_aktual_1 DECIMAL(8,2) NULL');
    }
};
