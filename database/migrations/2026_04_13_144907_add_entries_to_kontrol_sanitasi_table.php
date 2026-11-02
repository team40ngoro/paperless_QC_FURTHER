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
        Schema::table('kontrol_sanitasi', function (Blueprint $table) {
            if (!Schema::hasColumn('kontrol_sanitasi', 'entries')) {
                $table->longText('entries')->nullable()->after('jam');
            }
            
            // Commenting out change() for now to avoid Doctrine DBAL incompatibility
            // $table->string('suhu_air')->nullable()->change();
            // $table->string('kadar_klorin_food_basin')->nullable()->change();
            // $table->string('kadar_klorin_hand_basin')->nullable()->change();
            // $table->string('hasil_verifikasi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontrol_sanitasi', function (Blueprint $table) {
            //
        });
    }
};
