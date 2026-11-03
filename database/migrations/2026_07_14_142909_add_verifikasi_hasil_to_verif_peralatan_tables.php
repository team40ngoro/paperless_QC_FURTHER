<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('verif_peralatan_detail', function (Blueprint $table) {
            $table->boolean('verifikasi_hasil')->nullable()->after('tindakan_koreksi');
        });

        Schema::table('verif_peralatan_bagian', function (Blueprint $table) {
            $table->boolean('verifikasi_hasil')->nullable()->after('tindakan_koreksi');
        });
    }

    public function down()
    {
        Schema::table('verif_peralatan_detail', function (Blueprint $table) {
            $table->dropColumn('verifikasi_hasil');
        });

        Schema::table('verif_peralatan_bagian', function (Blueprint $table) {
            $table->dropColumn('verifikasi_hasil');
        });
    }
};
