<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToReleaseProdukFinishGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('release_produk_finish_goods', function (Blueprint $table) {
            $table->string('kode_form')->nullable();
            $table->boolean('approved_by_qc')->default(false);
            $table->foreignId('qc_approved_by')->nullable();
            $table->timestamp('qc_approved_at')->nullable();
            
            $table->boolean('approved_by_produksi')->default(false);
            $table->foreignId('produksi_approved_by')->nullable();
            $table->timestamp('produksi_approved_at')->nullable();
            
            $table->boolean('approved_by_spv')->default(false);
            $table->foreignId('spv_approved_by')->nullable();
            $table->timestamp('spv_approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('release_produk_finish_goods', function (Blueprint $table) {
            $table->dropColumn([
                'kode_form',
                'approved_by_qc',
                'qc_approved_by',
                'qc_approved_at',
                'approved_by_produksi',
                'produksi_approved_by',
                'produksi_approved_at',
                'approved_by_spv',
                'spv_approved_by',
                'spv_approved_at',
            ]);
        });
    }
}
