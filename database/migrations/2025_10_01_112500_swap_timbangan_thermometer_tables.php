<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup existing data
        $timbanganData = DB::table('timbangan')->get();
        $thermometerData = DB::table('thermometer')->get();

        // Drop existing tables
        Schema::dropIfExists('timbangan');
        Schema::dropIfExists('thermometer');

        // Create new timbangan table with old thermometer structure
        Schema::create('timbangan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->datetime('tanggal');
            $table->string('jenis');
            $table->string('kode_timbangan');
            $table->enum('hasil_pengecekan', ['ok', 'tidak_ok']);
            $table->enum('gram', ['500', '1000']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

            // Indexes
            $table->index(['id_plan', 'tanggal']);
            $table->index('user_id');
            $table->index('shift_id');
        });

        // Create new thermometer table with old timbangan structure
        Schema::create('thermometer', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->datetime('tanggal');
            $table->string('jenis');
            $table->string('kode_thermometer');
            $table->enum('hasil_pengecekan', ['ok', 'tidak_ok']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

            // Indexes untuk performa
            $table->index(['id_plan', 'tanggal']);
            $table->index('user_id');
            $table->index('shift_id');
        });

        // Restore data with swapped structure
        // Old thermometer data goes to new timbangan table
        foreach ($thermometerData as $record) {
            DB::table('timbangan')->insert([
                'uuid' => $record->uuid,
                'id_plan' => $record->id_plan,
                'user_id' => $record->user_id,
                'shift_id' => $record->shift_id,
                'tanggal' => $record->tanggal,
                'jenis' => $record->jenis,
                'kode_timbangan' => $record->kode_timbangan,
                'hasil_pengecekan' => $record->hasil_pengecekan,
                'gram' => $record->gram,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }

        // Old timbangan data goes to new thermometer table
        foreach ($timbanganData as $record) {
            DB::table('thermometer')->insert([
                'uuid' => $record->uuid,
                'id_plan' => $record->id_plan,
                'user_id' => $record->user_id,
                'shift_id' => $record->shift_id,
                'tanggal' => $record->tanggal,
                'jenis' => $record->jenis,
                'kode_thermometer' => $record->kode_thermometer,
                'hasil_pengecekan' => $record->hasil_pengecekan,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backup current data
        $timbanganData = DB::table('timbangan')->get();
        $thermometerData = DB::table('thermometer')->get();

        // Drop current tables
        Schema::dropIfExists('timbangan');
        Schema::dropIfExists('thermometer');

        // Recreate original timbangan table
        Schema::create('timbangan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->datetime('tanggal');
            $table->string('jenis');
            $table->string('kode_thermometer');
            $table->enum('hasil_pengecekan', ['ok', 'tidak_ok']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

            // Indexes untuk performa
            $table->index(['id_plan', 'tanggal']);
            $table->index('user_id');
            $table->index('shift_id');
        });

        // Recreate original thermometer table
        Schema::create('thermometer', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->datetime('tanggal');
            $table->string('jenis');
            $table->string('kode_timbangan');
            $table->enum('hasil_pengecekan', ['ok', 'tidak_ok']);
            $table->enum('gram', ['500', '1000']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');

            // Indexes
            $table->index(['id_plan', 'tanggal']);
            $table->index('user_id');
            $table->index('shift_id');
        });

        // Restore original data
        foreach ($thermometerData as $record) {
            DB::table('timbangan')->insert([
                'uuid' => $record->uuid,
                'id_plan' => $record->id_plan,
                'user_id' => $record->user_id,
                'shift_id' => $record->shift_id,
                'tanggal' => $record->tanggal,
                'jenis' => $record->jenis,
                'kode_thermometer' => $record->kode_thermometer,
                'hasil_pengecekan' => $record->hasil_pengecekan,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }

        foreach ($timbanganData as $record) {
            DB::table('thermometer')->insert([
                'uuid' => $record->uuid,
                'id_plan' => $record->id_plan,
                'user_id' => $record->user_id,
                'shift_id' => $record->shift_id,
                'tanggal' => $record->tanggal,
                'jenis' => $record->jenis,
                'kode_timbangan' => $record->kode_timbangan,
                'hasil_pengecekan' => $record->hasil_pengecekan,
                'gram' => $record->gram,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
    }
};
