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
        $tables = ['persiapan_bahan_forming', 'persiapan_bahan_non_forming'];
        
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'approved_by_qc')) {
                    $table->boolean('approved_by_qc')->default(false)->after('catatan');
                    $table->string('qc_approved_by')->nullable()->after('approved_by_qc');
                    $table->timestamp('qc_approved_at')->nullable()->after('qc_approved_by');
                }
                
                if (!Schema::hasColumn($tableName, 'approved_by_produksi')) {
                    $table->boolean('approved_by_produksi')->default(false)->after('qc_approved_at');
                    $table->string('produksi_approved_by')->nullable()->after('approved_by_produksi');
                    $table->timestamp('produksi_approved_at')->nullable()->after('produksi_approved_by');
                }
                
                if (!Schema::hasColumn($tableName, 'approved_by_spv')) {
                    $table->boolean('approved_by_spv')->default(false)->after('produksi_approved_at');
                    $table->string('spv_approved_by')->nullable()->after('approved_by_spv');
                    $table->timestamp('spv_approved_at')->nullable()->after('spv_approved_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['persiapan_bahan_forming', 'persiapan_bahan_non_forming'];
        
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = [
                    'approved_by_qc', 'qc_approved_by', 'qc_approved_at',
                    'approved_by_produksi', 'produksi_approved_by', 'produksi_approved_at',
                    'approved_by_spv', 'spv_approved_by', 'spv_approved_at'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
