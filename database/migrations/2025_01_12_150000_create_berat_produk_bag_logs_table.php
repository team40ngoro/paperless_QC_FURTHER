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
        Schema::create('berat_produk_bag_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('berat_produk_bag_id');
            $table->uuid('berat_produk_bag_uuid');
            $table->unsignedBigInteger('user_id');
            $table->json('field_yang_diubah');
            $table->json('nilai_lama');
            $table->json('nilai_baru');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('berat_produk_bag_id')->references('id')->on('berat_produk_bag')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('berat_produk_bag_id');
            $table->index('berat_produk_bag_uuid');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('berat_produk_bag_logs');
    }
};
