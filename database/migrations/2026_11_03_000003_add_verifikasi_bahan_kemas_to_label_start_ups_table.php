<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('label_start_ups', function (Blueprint $table) {
            $table->string('verifikasi_bahan_kemas', 20)->nullable();
            $table->unsignedBigInteger('verifikasi_bahan_kemas_by')->nullable();
            $table->timestamp('verifikasi_bahan_kemas_at')->nullable();
            $table->index('verifikasi_bahan_kemas');
        });
    }

    public function down(): void
    {
        Schema::table('label_start_ups', function (Blueprint $table) {
            $table->dropIndex(['verifikasi_bahan_kemas']);
            $table->dropColumn([
                'verifikasi_bahan_kemas',
                'verifikasi_bahan_kemas_by',
                'verifikasi_bahan_kemas_at',
            ]);
        });
    }
};
