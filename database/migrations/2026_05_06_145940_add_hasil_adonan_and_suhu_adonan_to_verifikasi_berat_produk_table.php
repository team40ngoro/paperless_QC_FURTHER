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
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            $table->string('hasil_adonan')->nullable()->after('pickup_total_roasting');
            $table->decimal('suhu_adonan', 5, 1)->nullable()->after('hasil_adonan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            $table->dropColumn(['hasil_adonan', 'suhu_adonan']);
        });
    }
};
