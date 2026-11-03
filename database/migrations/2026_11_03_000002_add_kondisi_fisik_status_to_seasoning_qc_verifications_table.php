<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasoning_qc_verifications', function (Blueprint $table) {
            $table->string('kondisi_fisik_status', 20)->nullable()->after('kemasan_status');
            $table->index('kondisi_fisik_status');
        });
    }

    public function down(): void
    {
        Schema::table('seasoning_qc_verifications', function (Blueprint $table) {
            $table->dropIndex(['kondisi_fisik_status']);
            $table->dropColumn('kondisi_fisik_status');
        });
    }
};
