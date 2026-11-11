<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chillroom_qc_verifications', function (Blueprint $table) {
            $table->string('pemeriksaan_kedatangan', 20)->nullable()->after('sensori');
            $table->string('sampling_berat_rm', 20)->nullable()->after('pemeriksaan_kedatangan');

            $table->index('pemeriksaan_kedatangan');
            $table->index('sampling_berat_rm');
        });
    }

    public function down(): void
    {
        Schema::table('chillroom_qc_verifications', function (Blueprint $table) {
            $table->dropIndex(['pemeriksaan_kedatangan']);
            $table->dropIndex(['sampling_berat_rm']);
            $table->dropColumn(['pemeriksaan_kedatangan', 'sampling_berat_rm']);
        });
    }
};
