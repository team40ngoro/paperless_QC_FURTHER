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
        Schema::table('proses_aging', function (Blueprint $table) {
            // Menambahkan kolom setelah id_produk
            $table->unsignedBigInteger('proses_tumbling_id')->nullable()->after('id_produk');
            $table->char('proses_tumbling_uuid', 36)->nullable()->after('proses_tumbling_id');

            // Menambahkan foreign key constraint
            $table->foreign('proses_tumbling_id', 'pr_aging_pr_tumbling_id_foreign')
                  ->references('id')
                  ->on('proses_tumbling')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_aging', function (Blueprint $table) {
            // Menghapus foreign key dan kolom
            $table->dropForeign('pr_aging_pr_tumbling_id_foreign');
            $table->dropColumn(['proses_tumbling_id', 'proses_tumbling_uuid']);
        });
    }
};