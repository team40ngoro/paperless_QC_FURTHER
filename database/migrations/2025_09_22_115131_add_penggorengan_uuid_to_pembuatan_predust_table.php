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
        Schema::table('pembuatan_predust', function (Blueprint $table) {
            $table->string('penggorengan_uuid')->nullable()->after('user_id');
            $table->index('penggorengan_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembuatan_predust', function (Blueprint $table) {
            $table->dropIndex(['penggorengan_uuid']);
            $table->dropColumn('penggorengan_uuid');
        });
    }
};
