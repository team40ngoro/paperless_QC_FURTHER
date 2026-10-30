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
        Schema::table('frayer_2', function (Blueprint $table) {
            // Drop foreign key constraints first
            if (Schema::hasColumn('frayer_2', 'id_suhu_frayer_1')) {
                $table->dropForeign(['id_suhu_frayer_1']);
                $table->dropColumn('id_suhu_frayer_1');
            }
            if (Schema::hasColumn('frayer_2', 'id_waktu_penggorengan')) {
                $table->dropForeign(['id_waktu_penggorengan']);
                $table->dropColumn('id_waktu_penggorengan');
            }
            
            // Add new columns only if they don't exist
            if (!Schema::hasColumn('frayer_2', 'id_suhu_frayer_2')) {
                $table->unsignedBigInteger('id_suhu_frayer_2')->nullable()->after('id_produk');
            }
            if (!Schema::hasColumn('frayer_2', 'id_waktu_penggorengan_2')) {
                $table->unsignedBigInteger('id_waktu_penggorengan_2')->nullable()->after('id_suhu_frayer_2');
            }
            
            // Add foreign key constraints if the referenced tables exist
            if (Schema::hasTable('suhu_frayer_2') && !Schema::hasColumn('frayer_2', 'id_suhu_frayer_2')) {
                $table->foreign('id_suhu_frayer_2')->references('id')->on('suhu_frayer_2')->onDelete('cascade');
            }
            if (Schema::hasTable('waktu_penggorengan_2') && !Schema::hasColumn('frayer_2', 'id_waktu_penggorengan_2')) {
                $table->foreign('id_waktu_penggorengan_2')->references('id')->on('waktu_penggorengan_2')->onDelete('cascade');
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
        Schema::table('frayer_2', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('frayer_2', 'id_suhu_frayer_2')) {
                $table->dropForeign(['id_suhu_frayer_2']);
                $table->dropColumn('id_suhu_frayer_2');
            }
            if (Schema::hasColumn('frayer_2', 'id_waktu_penggorengan_2')) {
                $table->dropForeign(['id_waktu_penggorengan_2']);
                $table->dropColumn('id_waktu_penggorengan_2');
            }
            
            // Add back old columns
            $table->unsignedBigInteger('id_suhu_frayer_1')->nullable()->after('id_produk');
            $table->unsignedBigInteger('id_waktu_penggorengan')->nullable()->after('id_suhu_frayer_1');
        });
    }
};
