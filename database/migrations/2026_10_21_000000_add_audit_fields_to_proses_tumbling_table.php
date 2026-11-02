<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('proses_tumbling', 'is_audit')) {
            Schema::table('proses_tumbling', function (Blueprint $table) {
                $table->boolean('is_audit')->default(false)->after('uuid');
            });
        }

        if (!Schema::hasColumn('proses_tumbling', 'source_uuid')) {
            Schema::table('proses_tumbling', function (Blueprint $table) {
                $table->string('source_uuid', 36)->nullable()->after('is_audit');
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        if (Schema::hasColumn('proses_tumbling', 'source_uuid')) {
            $columns[] = 'source_uuid';
        }

        if (Schema::hasColumn('proses_tumbling', 'is_audit')) {
            $columns[] = 'is_audit';
        }

        if ($columns) {
            Schema::table('proses_tumbling', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
