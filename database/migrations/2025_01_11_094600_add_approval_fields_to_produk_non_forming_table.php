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
        Schema::table('produk_non_forming', function (Blueprint $table) {
            // Boolean approval fields
            $table->boolean('approved_by_qc')->default(false)->after('verifikasi');
            $table->boolean('approved_by_produksi')->default(false)->after('approved_by_qc');
            $table->boolean('approved_by_spv')->default(false)->after('approved_by_produksi');
            
            // User ID fields for who approved
            $table->unsignedBigInteger('qc_approved_by')->nullable()->after('approved_by_spv');
            $table->unsignedBigInteger('produksi_approved_by')->nullable()->after('qc_approved_by');
            $table->unsignedBigInteger('spv_approved_by')->nullable()->after('produksi_approved_by');
            
            // Timestamp fields for when approved
            $table->timestamp('qc_approved_at')->nullable()->after('spv_approved_by');
            $table->timestamp('produksi_approved_at')->nullable()->after('qc_approved_at');
            $table->timestamp('spv_approved_at')->nullable()->after('produksi_approved_at');
            
            // Foreign key constraints with shorter names
            $table->foreign('qc_approved_by', 'pnf_qc_approved_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('produksi_approved_by', 'pnf_produksi_approved_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('spv_approved_by', 'pnf_spv_approved_fk')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'], 'pnf_approval_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_non_forming', function (Blueprint $table) {
            // Drop foreign keys first using the constraint names
            $table->dropForeign('pnf_qc_approved_fk');
            $table->dropForeign('pnf_produksi_approved_fk');
            $table->dropForeign('pnf_spv_approved_fk');
            
            // Drop indexes
            $table->dropIndex('pnf_approval_status_idx');
            
            // Drop columns
            $table->dropColumn([
                'approved_by_qc',
                'approved_by_produksi', 
                'approved_by_spv',
                'qc_approved_by',
                'produksi_approved_by',
                'spv_approved_by',
                'qc_approved_at',
                'produksi_approved_at',
                'spv_approved_at'
            ]);
        });
    }
};
