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
        Schema::table('proses_frayer', function (Blueprint $table) {
            // Tambah kolom UUID untuk relasi dengan proses sebelumnya
            $table->uuid('penggorengan_uuid')->nullable()->after('uuid');
            $table->uuid('predust_uuid')->nullable()->after('penggorengan_uuid');
            $table->uuid('battering_uuid')->nullable()->after('predust_uuid');
            $table->uuid('breader_uuid')->nullable()->after('battering_uuid');
            
            // Tambah index untuk performa query
            $table->index('penggorengan_uuid');
            $table->index('predust_uuid');
            $table->index('battering_uuid');
            $table->index('breader_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_frayer', function (Blueprint $table) {
            // Drop index terlebih dahulu
            $table->dropIndex(['penggorengan_uuid']);
            $table->dropIndex(['predust_uuid']);
            $table->dropIndex(['battering_uuid']);
            $table->dropIndex(['breader_uuid']);
            
            // Drop kolom UUID
            $table->dropColumn([
                'penggorengan_uuid',
                'predust_uuid', 
                'battering_uuid',
                'breader_uuid'
            ]);
        });
    }
};
