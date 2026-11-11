<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shoestring_qc_verifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('shoestring_id');
            $table->uuid('shoestring_uuid')->nullable();
            $table->string('verifikasi', 20);
            $table->text('sampling_defect')->nullable();
            $table->json('sampling_defect_qty')->nullable();
            $table->string('total_defect', 50)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['shoestring_id', 'verified_at'], 'shoestring_qc_verif_item_time_index');
            $table->index('verifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shoestring_qc_verifications');
    }
};
