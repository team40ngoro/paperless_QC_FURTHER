<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master CIP: menyimpan standar suhu (°C) dan waktu (detik) untuk setiap tahap proses CIP.
     * Digunakan sebagai referensi/default pada form Verifikasi CIP.
     */
    public function up(): void
    {
        Schema::create('master_cip', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_plan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            // Key tahap: ro1, ri1, ro2, ri2, hc, hci, ro3, ri3, dis, diso, ro4, ri4
            $table->string('step_key', 20)->comment('Kode tahap, misal: ro1, ri1, hc, dst');
            $table->string('step_label', 100)->nullable()->comment('Nama label tahap');

            $table->decimal('std_suhu', 8, 2)->nullable()->comment('Standar suhu (°C)');
            $table->decimal('std_waktu', 10, 2)->nullable()->comment('Standar waktu (detik)');
            $table->string('keterangan', 255)->nullable();

            $table->timestamps();

            $table->index('id_plan');
            $table->index('user_id');
            $table->index(['id_plan', 'step_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_cip');
    }
};
