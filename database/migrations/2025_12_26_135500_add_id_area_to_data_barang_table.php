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
        Schema::table('data_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('id_area')->nullable()->after('id_plan');
            $table->foreign('id_area')->references('id')->on('input_area')->onDelete('set null');
            $table->index(['id_plan', 'id_area']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_barang', function (Blueprint $table) {
            $table->dropForeign(['id_area']);
            $table->dropIndex(['id_plan', 'id_area']);
            $table->dropColumn('id_area');
        });
    }
};
