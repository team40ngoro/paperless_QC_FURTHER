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
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_mudah_pecah', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->after('spv_approved_at');
            }
            if (!Schema::hasColumn('barang_mudah_pecah', 'source_uuid')) {
                $table->string('source_uuid', 36)->nullable()->after('is_audit');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barang_mudah_pecah', function (Blueprint $table) {
            if (Schema::hasColumn('barang_mudah_pecah', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
            if (Schema::hasColumn('barang_mudah_pecah', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }
        });
    }
};
