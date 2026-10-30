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
        Schema::table('pemeriksaan_produk_cooking_mixer_fla', function (Blueprint $table) {
            $table->enum('organo_rasa', ['OK', 'Tidak OK'])->after('organo_tekstur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_produk_cooking_mixer_fla', function (Blueprint $table) {
            $table->dropColumn('organo_rasa');
        });
    }
};
