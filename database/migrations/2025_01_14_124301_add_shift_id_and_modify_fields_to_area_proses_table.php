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
        Schema::table('area_proses', function (Blueprint $table) {
            // Add shift_id field
            $table->unsignedBigInteger('shift_id')->nullable()->after('area_id');
            
            // Drop existing enum constraints and recreate as text
            $table->dropColumn(['ketidaksesuaian', 'tindakan_koreksi']);
        });
        
        // Add the new text columns
        Schema::table('area_proses', function (Blueprint $table) {
            $table->text('ketidaksesuaian')->nullable()->after('pemeriksaan_suhu_ruang');
            $table->text('tindakan_koreksi')->nullable()->after('ketidaksesuaian');
            
            // Add foreign key for shift_id
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('set null');
            
            // Add index for shift_id
            $table->index('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('area_proses', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['shift_id']);
            $table->dropIndex(['shift_id']);
            $table->dropColumn('shift_id');
            
            // Drop text columns
            $table->dropColumn(['ketidaksesuaian', 'tindakan_koreksi']);
        });
        
        // Recreate enum columns
        Schema::table('area_proses', function (Blueprint $table) {
            $table->enum('ketidaksesuaian', ['OK', 'Tidak OK'])->after('pemeriksaan_suhu_ruang');
            $table->enum('tindakan_koreksi', ['OK', 'Tidak OK'])->after('ketidaksesuaian');
        });
    }
};
