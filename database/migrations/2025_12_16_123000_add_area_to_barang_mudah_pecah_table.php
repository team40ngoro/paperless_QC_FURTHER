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
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->unsignedBigInteger('id_area')->nullable()->after('jumlah');
            $table->unsignedBigInteger('id_sub_area')->nullable()->after('id_area');

            $table->foreign('id_area')->references('id')->on('input_area')->onDelete('set null');
            $table->foreign('id_sub_area')->references('id')->on('sub_area')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            $table->dropForeign(['id_area']);
            $table->dropForeign(['id_sub_area']);
            $table->dropColumn(['id_area', 'id_sub_area']);
        });
    }
};
