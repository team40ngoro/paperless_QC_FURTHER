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
        Schema::create('pemeriksaan_rheon_machine', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('id_produk');
            $table->datetime('tanggal');
            $table->string('batch');
            $table->time('pukul');
            
            // Setting Rheon Machine
            $table->string('inner')->nullable();
            $table->string('outer')->nullable();
            $table->string('belt')->nullable();
            $table->string('extrusion_speed')->nullable();
            $table->string('jenis_cetakan')->nullable();
            $table->string('outlet_cvg')->nullable();
            
            // Dynamic form arrays - stored as JSON
            $table->json('berat_dough_adonan')->nullable();
            $table->json('berat_filler')->nullable();
            $table->json('berat_after_forming')->nullable();
            $table->json('berat_after_frying')->nullable();
            
            // Auto-calculated fields
            $table->decimal('jumlah_dough', 10, 2)->default(0);
            $table->decimal('rata_rata_dough', 10, 2)->default(0);
            $table->decimal('jumlah_filler', 10, 2)->default(0);
            $table->decimal('rata_rata_filler', 10, 2)->default(0);
            $table->decimal('jumlah_after_forming', 10, 2)->default(0);
            $table->decimal('rata_rata_after_forming', 10, 2)->default(0);
            $table->decimal('jumlah_after_frying', 10, 2)->default(0);
            $table->decimal('rata_rata_after_frying', 10, 2)->default(0);
            
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_plan')->references('id')->on('plan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('data_shift')->onDelete('cascade');
            $table->foreign('id_produk')->references('id')->on('jenis_produk')->onDelete('cascade');
            
            // Indexes
            $table->index(['id_plan', 'tanggal']);
            $table->index(['user_id', 'tanggal']);
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemeriksaan_rheon_machine');
    }
};
