<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_standar_audits', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('module_name'); // Nama modul/form (sesuai Akses Kontrol, misal: Chillroom, Seasoning, Pemasakan Nasi, dll)
            $table->string('table_name')->nullable(); // Nama tabel DB (misal: penerimaan_chillroom)
            $table->string('column_name'); // Nama kolom DB (misal: suhu, suhu_ruang, suhu_kondisi, suhu_pusat, suhu_pemasakan, sensori, status_rm)
            $table->enum('parameter_type', ['suhu', 'suhu_ruang', 'suhu_kondisi', 'suhu_pusat', 'suhu_pemasakan', 'status_checklist']); 
            $table->decimal('min_value', 8, 2)->nullable(); // Batas Min
            $table->decimal('max_value', 8, 2)->nullable(); // Batas Max
            $table->decimal('standar_value', 8, 2)->nullable(); // Nilai Otomatis Audit jika diluar min-max
            $table->boolean('auto_fix_status')->default(true); // Otomatis ubah Silang / NG / Reject -> OK / Ceklis
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_standar_audits');
    }
};
