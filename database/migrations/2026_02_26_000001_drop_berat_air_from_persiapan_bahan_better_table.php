<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            if (Schema::hasColumn('persiapan_bahan_better', 'berat_air')) {
                $table->dropColumn('berat_air');
            }
        });
    }

    public function down()
    {
        Schema::table('persiapan_bahan_better', function (Blueprint $table) {
            if (!Schema::hasColumn('persiapan_bahan_better', 'berat_air')) {
                $table->decimal('berat_air', 10, 2)->after('berat_better');
            }
        });
    }
};
