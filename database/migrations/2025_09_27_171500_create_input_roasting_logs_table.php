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
        Schema::create('input_roasting_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('input_roasting_id');
            $table->uuid('input_roasting_uuid');
            $table->unsignedBigInteger('user_id');
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('input_roasting_id', 'ir_logs_ir_id_foreign')->references('id')->on('input_roasting')->onDelete('cascade');
            $table->foreign('user_id', 'ir_logs_user_id_foreign')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('input_roasting_uuid', 'ir_logs_ir_uuid_index');
            $table->index('user_id', 'ir_logs_user_id_index');
            $table->index('created_at', 'ir_logs_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('input_roasting_logs');
    }
};
