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
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE pemeriksaan_produk_cooking_mixer_fla MODIFY id_stp_frm_fla BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE pemeriksaan_produk_cooking_mixer_fla MODIFY id_stp_frm_fla BIGINT UNSIGNED NOT NULL');
    }
};
