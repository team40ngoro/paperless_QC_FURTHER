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
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->string('suhu_hasil_tumbling')->nullable()->after('aktual_tekanan_non_vakum');
            $table->string('sensori_hasil_tumbling')->nullable()->after('suhu_hasil_tumbling');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->dropColumn(['suhu_hasil_tumbling', 'sensori_hasil_tumbling']);
        });
    }
};
