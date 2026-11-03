<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasoning_qc_verifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('seasoning_id');
            $table->uuid('seasoning_uuid')->nullable();
            $table->string('data_status', 20);
            $table->string('sensori_status', 20);
            $table->string('kemasan_status', 20);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['seasoning_id', 'verified_at'], 'seasoning_qc_verif_item_time_index');
            $table->index('data_status');
            $table->index('sensori_status');
            $table->index('kemasan_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasoning_qc_verifications');
    }
};
