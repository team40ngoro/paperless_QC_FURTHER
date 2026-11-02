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
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->boolean('approved_by_qc')->default(false)->after('sensori_hasil_tumbling');
            $table->unsignedBigInteger('qc_approved_by')->nullable()->after('approved_by_qc');
            $table->timestamp('qc_approved_at')->nullable()->after('qc_approved_by');

            $table->boolean('approved_by_produksi')->default(false)->after('qc_approved_at');
            $table->unsignedBigInteger('produksi_approved_by')->nullable()->after('approved_by_produksi');
            $table->timestamp('produksi_approved_at')->nullable()->after('produksi_approved_by');

            $table->boolean('approved_by_spv')->default(false)->after('produksi_approved_at');
            $table->unsignedBigInteger('spv_approved_by')->nullable()->after('approved_by_spv');
            $table->timestamp('spv_approved_at')->nullable()->after('spv_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_tumbling', function (Blueprint $table) {
            $table->dropColumn([
                'approved_by_qc', 'qc_approved_by', 'qc_approved_at',
                'approved_by_produksi', 'produksi_approved_by', 'produksi_approved_at',
                'approved_by_spv', 'spv_approved_by', 'spv_approved_at'
            ]);
        });
    }
};
