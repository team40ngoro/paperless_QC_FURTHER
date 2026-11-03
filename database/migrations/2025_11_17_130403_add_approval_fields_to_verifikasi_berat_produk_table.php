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
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
             $table->boolean('approved_by_qc')->default(false)->after('kode_form');
            $table->boolean('approved_by_spv')->default(false)->after('approved_by_qc');
            $table->boolean('approved_by_produksi')->default(false)->after('approved_by_spv');
            $table->unsignedBigInteger('qc_approved_by')->nullable()->after('approved_by_produksi');
            $table->unsignedBigInteger('spv_approved_by')->nullable()->after('qc_approved_by');
            $table->unsignedBigInteger('produksi_approved_by')->nullable()->after('spv_approved_by');
            $table->timestamp('qc_approved_at')->nullable()->after('produksi_approved_by');
            $table->timestamp('spv_approved_at')->nullable()->after('qc_approved_at');
            $table->timestamp('produksi_approved_at')->nullable()->after('spv_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verifikasi_berat_produk', function (Blueprint $table) {
            $table->dropColumn([
                'approved_by_qc',
                'approved_by_spv',
                'approved_by_produksi',
                'qc_approved_by',
                'spv_approved_by',
                'produksi_approved_by',
                'qc_approved_at',
                'spv_approved_at',
                'produksi_approved_at'
            ]);
        });
    }
};
