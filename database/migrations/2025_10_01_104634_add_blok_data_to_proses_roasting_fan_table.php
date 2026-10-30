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
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            if (!Schema::hasColumn('proses_roasting_fan', 'blok_data')) {
                $table->json('blok_data')->nullable()->after('id_produk');
            }
            if (!Schema::hasColumn('proses_roasting_fan', 'is_grouped')) {
                $table->boolean('is_grouped')->default(false)->after('blok_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            $table->dropColumn(['blok_data', 'is_grouped']);
        });
    }
};
