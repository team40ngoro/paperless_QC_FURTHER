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
        DB::statement("ALTER TABLE penerimaan_chillroom MODIFY nilai_jumlah_rm LONGTEXT NULL");
        DB::statement("ALTER TABLE penerimaan_chillroom MODIFY jumlah_rm LONGTEXT NULL");
        DB::statement("ALTER TABLE penerimaan_chillroom MODIFY keterangan TEXT NULL");
        DB::statement("ALTER TABLE penerimaan_chillroom MODIFY catatan_rm TEXT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE penerimaan_chillroom MODIFY nilai_jumlah_rm VARCHAR(255) NULL");
        DB::statement("ALTER TABLE penerimaan_chillroom MODIFY jumlah_rm VARCHAR(255) NULL");
    }
};
