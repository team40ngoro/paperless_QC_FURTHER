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
        Schema::table('std_fan', function (Blueprint $table) {
            $table->string('std_fan_2')->after('std_fan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('std_fan', function (Blueprint $table) {
            $table->dropColumn('std_fan_2');
        });
    }
};
