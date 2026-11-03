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
        Schema::table('pemeriksaan_benda_asing', function (Blueprint $table) {
            $table->string('rm', 255)->nullable()->after('id_produk');
            $table->string('jumlah_terdampak', 255)->nullable()->after('rm');
        });
    }

    public function down()
    {
        Schema::table('pemeriksaan_benda_asing', function (Blueprint $table) {
            $table->dropColumn(['rm', 'jumlah_terdampak']);
        });
    }
};
