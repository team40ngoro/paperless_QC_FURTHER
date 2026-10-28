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
            $table->string('ffa')->nullable()->after('tpm_minyak');
        });

        Schema::table('frayer_2', function (Blueprint $table) {
            $table->string('ffa')->nullable()->after('tpm_minyak');
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
            $table->dropColumn('ffa');
        });

        Schema::table('frayer_2', function (Blueprint $table) {
            $table->dropColumn('ffa');
        });
    }
};
