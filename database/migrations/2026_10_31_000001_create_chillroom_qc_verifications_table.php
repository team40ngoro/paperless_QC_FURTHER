<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chillroom_qc_verifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('penerimaan_chillroom_id');
            $table->uuid('penerimaan_chillroom_uuid')->nullable();
            $table->string('status', 20);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['penerimaan_chillroom_id', 'verified_at'], 'chillroom_qc_verif_item_time_index');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chillroom_qc_verifications');
    }
};
