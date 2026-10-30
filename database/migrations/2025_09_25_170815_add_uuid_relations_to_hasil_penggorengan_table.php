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
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil_penggorengan', 'penggorengan_uuid')) {
                $table->uuid('penggorengan_uuid')->nullable()->after('id');
                $table->foreign('penggorengan_uuid')->references('uuid')->on('penggorengan')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('hasil_penggorengan', 'predust_uuid')) {
                $table->uuid('predust_uuid')->nullable()->after('penggorengan_uuid');
                $table->foreign('predust_uuid')->references('uuid')->on('pembuatan_predust')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('hasil_penggorengan', 'battering_uuid')) {
                $table->uuid('battering_uuid')->nullable()->after('predust_uuid');
                $table->foreign('battering_uuid')->references('uuid')->on('proses_battering')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('hasil_penggorengan', 'breader_uuid')) {
                $table->uuid('breader_uuid')->nullable()->after('battering_uuid');
                $table->foreign('breader_uuid')->references('uuid')->on('proses_breader')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('hasil_penggorengan', 'frayer_uuid')) {
                $table->uuid('frayer_uuid')->nullable()->after('breader_uuid');
                $table->foreign('frayer_uuid')->references('uuid')->on('proses_frayer')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            // Drop foreign keys if they exist
            if (Schema::hasColumn('hasil_penggorengan', 'penggorengan_uuid')) {
                $table->dropForeign(['penggorengan_uuid']);
            }
            if (Schema::hasColumn('hasil_penggorengan', 'predust_uuid')) {
                $table->dropForeign(['predust_uuid']);
            }
            if (Schema::hasColumn('hasil_penggorengan', 'battering_uuid')) {
                $table->dropForeign(['battering_uuid']);
            }
            if (Schema::hasColumn('hasil_penggorengan', 'breader_uuid')) {
                $table->dropForeign(['breader_uuid']);
            }
            if (Schema::hasColumn('hasil_penggorengan', 'frayer_uuid')) {
                $table->dropForeign(['frayer_uuid']);
            }
            
            // Drop columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('hasil_penggorengan', 'penggorengan_uuid')) {
                $columnsToDrop[] = 'penggorengan_uuid';
            }
            if (Schema::hasColumn('hasil_penggorengan', 'predust_uuid')) {
                $columnsToDrop[] = 'predust_uuid';
            }
            if (Schema::hasColumn('hasil_penggorengan', 'battering_uuid')) {
                $columnsToDrop[] = 'battering_uuid';
            }
            if (Schema::hasColumn('hasil_penggorengan', 'breader_uuid')) {
                $columnsToDrop[] = 'breader_uuid';
            }
            if (Schema::hasColumn('hasil_penggorengan', 'frayer_uuid')) {
                $columnsToDrop[] = 'frayer_uuid';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
