<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk membuat tabel log shoestring
     */
    public function up()
    {
        Schema::create('shoestring_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            
            // Referensi ke data yang diubah
            $table->unsignedBigInteger('shoestring_id');
            $table->string('shoestring_uuid');
            
            // Informasi user yang melakukan perubahan
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_role');
            
            // Detail perubahan
            $table->string('aksi')->default('update'); // update, create, delete
            $table->text('field_yang_diubah'); // JSON field names yang berubah
            $table->longText('nilai_lama')->nullable(); // JSON nilai sebelum diubah
            $table->longText('nilai_baru')->nullable(); // JSON nilai setelah diubah
            
            // Informasi tambahan
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('keterangan')->nullable(); // Keterangan tambahan jika ada
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('shoestring_id');
            $table->index('user_id');
            $table->index('created_at');
            
            // Foreign key constraints
            $table->foreign('shoestring_id')
                  ->references('id')
                  ->on('shoestrings')
                  ->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Membatalkan migration
     */
    public function down()
    {
        Schema::dropIfExists('shoestring_logs');
    }
};
