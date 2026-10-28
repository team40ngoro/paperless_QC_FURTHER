<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('dokumentasi', function (Blueprint $table) {
            $table->string('label_supplier')->nullable()->after('label_polyroll');
        });
        
        DB::statement('ALTER TABLE dokumentasi MODIFY qr_code VARCHAR(255) NULL;');
        DB::statement('ALTER TABLE dokumentasi MODIFY label_polyroll VARCHAR(255) NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE dokumentasi MODIFY qr_code VARCHAR(255) NOT NULL;');
        DB::statement('ALTER TABLE dokumentasi MODIFY label_polyroll VARCHAR(255) NOT NULL;');

        Schema::table('dokumentasi', function (Blueprint $table) {
            $table->dropColumn('label_supplier');
        });
    }
};
