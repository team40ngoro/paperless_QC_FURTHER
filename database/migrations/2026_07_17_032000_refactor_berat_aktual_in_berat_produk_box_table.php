<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Rename berat_aktual_1 → berat_aktual (TEXT)
        DB::statement('ALTER TABLE berat_produk_box CHANGE berat_aktual_1 berat_aktual TEXT');

        // 2. Migrate existing data: merge 1/2/3 into JSON array
        $records = DB::table('berat_produk_box')->get();
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

            DB::table('berat_produk_box')
                ->where('id', $record->id)
                ->update(['berat_aktual' => json_encode($array)]);
        }

        // 3. Drop old columns
        Schema::table('berat_produk_box', function (Blueprint $table) {
            $table->dropColumn(['berat_aktual_2', 'berat_aktual_3']);
        });
    }

    public function down()
    {
        Schema::table('berat_produk_box', function (Blueprint $table) {
            $table->float('berat_aktual_2')->nullable();
            $table->float('berat_aktual_3')->nullable();
        });

        $records = DB::table('berat_produk_box')->get();
        foreach ($records as $record) {
            $decoded = json_decode($record->berat_aktual, true);
            DB::table('berat_produk_box')
                ->where('id', $record->id)
                ->update([
                    'berat_aktual'   => $decoded[0] ?? $record->berat_aktual,
                    'berat_aktual_2' => $decoded[1] ?? null,
                    'berat_aktual_3' => $decoded[2] ?? null,
                ]);
        }

        DB::statement('ALTER TABLE berat_produk_box CHANGE berat_aktual berat_aktual_1 FLOAT NULL');
    }
};
