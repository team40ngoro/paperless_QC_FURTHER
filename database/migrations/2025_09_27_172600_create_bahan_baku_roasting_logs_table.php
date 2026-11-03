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
        Schema::create('bahan_baku_roasting_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('bahan_baku_roasting_id');
            $table->uuid('bahan_baku_roasting_uuid');
            $table->unsignedBigInteger('user_id');
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('bahan_baku_roasting_id', 'bbr_logs_bbr_id_foreign')->references('id')->on('bahan_baku_roasting')->onDelete('cascade');
            $table->foreign('user_id', 'bbr_logs_user_id_foreign')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('bahan_baku_roasting_uuid', 'bbr_logs_bbr_uuid_index');
            $table->index('user_id', 'bbr_logs_user_id_index');
            $table->index('created_at', 'bbr_logs_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bahan_baku_roasting_logs');
    }
};
