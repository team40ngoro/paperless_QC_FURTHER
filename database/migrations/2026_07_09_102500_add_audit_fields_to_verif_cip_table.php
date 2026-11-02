<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuditFieldsToVerifCipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verif_cip', function (Blueprint $table) {
            if (!Schema::hasColumn('verif_cip', 'is_audit')) {
                $table->boolean('is_audit')->default(false)->after('payload');
            }
            if (!Schema::hasColumn('verif_cip', 'source_uuid')) {
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
        Schema::table('verif_cip', function (Blueprint $table) {
            if (Schema::hasColumn('verif_cip', 'is_audit')) {
                $table->dropColumn('is_audit');
            }
            if (Schema::hasColumn('verif_cip', 'source_uuid')) {
                $table->dropColumn('source_uuid');
            }
        });
    }
}
