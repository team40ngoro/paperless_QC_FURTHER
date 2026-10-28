<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('data_box', function (Blueprint $table) {
            $table->integer('std_isi_pack_karton')->nullable()->after('berat');
        });
    }

    public function down()
    {
        Schema::table('data_box', function (Blueprint $table) {
            $table->dropColumn('std_isi_pack_karton');
        });
    }
};
