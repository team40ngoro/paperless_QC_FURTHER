<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->string('sensori_kematangan')->nullable()->after('aktual_suhu_pusat');
            $table->string('sensori_kenampakan')->nullable()->after('sensori_kematangan');
            $table->string('sensori_warna')->nullable()->after('sensori_kenampakan');
            $table->string('sensori_rasa')->nullable()->after('sensori_warna');
            $table->string('sensori_bau')->nullable()->after('sensori_rasa');
            $table->string('sensori_tekstur')->nullable()->after('sensori_bau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            $table->dropColumn([
                'sensori_kematangan',
                'sensori_kenampakan',
                'sensori_warna',
                'sensori_rasa',
                'sensori_bau',
                'sensori_tekstur',
            ]);
        });
    }
};
