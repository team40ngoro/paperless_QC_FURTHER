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
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            // Add approval columns
            $table->boolean('approved_by_qc')->default(false)->after('tindakan_koreksi');
            $table->boolean('approved_by_produksi')->default(false)->after('approved_by_qc');
            $table->boolean('approved_by_spv')->default(false)->after('approved_by_produksi');
            
            // Add approval timestamps
            $table->timestamp('approved_by_qc_at')->nullable()->after('approved_by_spv');
            $table->timestamp('approved_by_produksi_at')->nullable()->after('approved_by_qc_at');
            $table->timestamp('approved_by_spv_at')->nullable()->after('approved_by_produksi_at');
            
            // Add approval user IDs
            $table->unsignedBigInteger('approved_by_qc_user_id')->nullable()->after('approved_by_spv_at');
            $table->unsignedBigInteger('approved_by_produksi_user_id')->nullable()->after('approved_by_qc_user_id');
            $table->unsignedBigInteger('approved_by_spv_user_id')->nullable()->after('approved_by_produksi_user_id');
            
            // Add foreign key constraints for approval users
            $table->foreign('approved_by_qc_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_produksi_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_spv_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['approved_by_qc_user_id']);
            $table->dropForeign(['approved_by_produksi_user_id']);
            $table->dropForeign(['approved_by_spv_user_id']);
            
            // Drop approval columns
            $table->dropColumn([
                'approved_by_qc',
                'approved_by_produksi',
                'approved_by_spv',
                'approved_by_qc_at',
                'approved_by_produksi_at',
                'approved_by_spv_at',
                'approved_by_qc_user_id',
                'approved_by_produksi_user_id',
                'approved_by_spv_user_id'
            ]);
        });
    }
};
