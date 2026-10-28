<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verif_peralatan_detail', function (Blueprint $table) {
            // Buat index pengganti dulu (agar FK `verif_peralatan_id` tetap punya index)
            $table->unique(['verif_peralatan_id', 'id_mesin'], 'vp_detail_mesin_unique');

            // Lepas FK yang terkait kolom yang akan dihapus
            $table->dropForeign(['id_sub_area']);

            // Hapus unique lama yang masih menyertakan id_sub_area
            $table->dropUnique('vp_detail_unique');

            // Hapus kolom
            $table->dropColumn('id_sub_area');
        });
    }

    public function down(): void
    {
        Schema::table('verif_peralatan_detail', function (Blueprint $table) {
            // Tambahkan kembali kolom
            $table->unsignedBigInteger('id_sub_area')->nullable();

            // Hapus unique versi baru
            $table->dropUnique('vp_detail_mesin_unique');

            // Kembalikan FK dan unique lama
            $table->foreign('id_sub_area')->references('id')->on('sub_area')->onDelete('cascade');
            $table->unique(['verif_peralatan_id', 'id_mesin', 'id_sub_area'], 'vp_detail_unique');
        });
    }
};
