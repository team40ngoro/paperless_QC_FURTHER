<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            if (Schema::hasColumn('hasil_penggorengan', 'sensori')) {
                $table->dropColumn('sensori');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_penggorengan', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil_penggorengan', 'sensori')) {
                $table->string('sensori')->nullable()->after('aktual_suhu_pusat');
            }
        });
    }
};
