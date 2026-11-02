<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            if (!Schema::hasColumn('verifikasi_berat_produk', 'gramase')) {
                $table->decimal('gramase', 10, 2)->nullable()->after('kode_produksi');
            }

            if (!Schema::hasColumn('verifikasi_berat_produk', 'catatan')) {
                $table->text('catatan')->nullable()->after('gramase');
            }
        });
    }

    public function down()
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            if (Schema::hasColumn('verifikasi_berat_produk', 'catatan')) {
                $table->dropColumn('catatan');
            }

            if (Schema::hasColumn('verifikasi_berat_produk', 'gramase')) {
                $table->dropColumn('gramase');
            }
        });
    }
};
