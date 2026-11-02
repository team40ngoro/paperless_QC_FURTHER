<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hasil_proses_roasting_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('hasil_proses_roasting_id');
            $table->uuid('hasil_proses_roasting_uuid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('hasil_proses_roasting_id')
                ->references('id')->on('hasil_proses_roasting')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('hasil_proses_roasting_uuid');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('hasil_proses_roasting_logs');
    }
};
