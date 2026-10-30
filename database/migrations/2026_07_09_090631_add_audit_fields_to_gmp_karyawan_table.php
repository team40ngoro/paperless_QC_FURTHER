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
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('gmp_karyawan', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->after('id');
            }
            if (!Schema::hasColumn('gmp_karyawan', 'source_uuid')) {
                $table->uuid('source_uuid')->nullable()->after('is_audit');
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
        Schema::table('gmp_karyawan', function (Blueprint $table) {
            //
        });
    }
};
