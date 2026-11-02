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
        Schema::create('pembekuan_iqf_penggorengan_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('pembekuan_iqf_penggorengan_id');
            $table->uuid('pembekuan_iqf_penggorengan_uuid');
            $table->unsignedBigInteger('user_id');
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pembekuan_iqf_penggorengan_id', 'piqf_logs_piqf_id_foreign')->references('id')->on('pembekuan_iqf_penggorengan')->onDelete('cascade');
            $table->foreign('user_id', 'piqf_logs_user_id_foreign')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('pembekuan_iqf_penggorengan_uuid', 'piqf_logs_piqf_uuid_index');
            $table->index('user_id', 'piqf_logs_user_id_index');
            $table->index('created_at', 'piqf_logs_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembekuan_iqf_penggorengan_logs');
    }
};
