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
        Schema::table('produk_non_forming', function (Blueprint $table) {
            // Add kode_form field after uuid
            $table->string('kode_form', 50)->nullable()->after('uuid');
            
            // Add index for better performance when filtering
            $table->index('kode_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_non_forming', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['kode_form']);
            
            // Drop the column
            $table->dropColumn('kode_form');
        });
    }
};
