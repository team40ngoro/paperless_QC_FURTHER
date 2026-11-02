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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom uuid jika belum ada
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->string('uuid', 36)->unique()->nullable()->after('id');
            }
            // Tambah kolom role jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('email');
            }
            // Tambah kolom id_plan jika belum ada
            if (!Schema::hasColumn('users', 'id_plan')) {
                $table->unsignedBigInteger('id_plan')->nullable()->after('role');
                $table->foreign('id_plan')->references('id')->on('plan')->onDelete('set null');
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
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key dan kolom id_plan jika ada
            if (Schema::hasColumn('users', 'id_plan')) {
                $table->dropForeign(['id_plan']);
                $table->dropColumn('id_plan');
            }
            // Hapus kolom role jika ada
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            // Hapus kolom uuid jika ada
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }
};
