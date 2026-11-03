<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proses_twahing', function (Blueprint $table) {
            $table->string('group_uuid')->collation('utf8mb4_unicode_ci')->nullable()->after('uuid')->index();
        });

        // Set existing records: group_uuid = uuid (each is its own parent)
        \DB::table('proses_twahing')->whereNull('group_uuid')->update([
            'group_uuid' => \DB::raw('uuid'),
        ]);
    }

    public function down(): void
    {
        Schema::table('proses_twahing', function (Blueprint $table) {
            $table->dropIndex(['group_uuid']);
            $table->dropColumn('group_uuid');
        });
    }
};
