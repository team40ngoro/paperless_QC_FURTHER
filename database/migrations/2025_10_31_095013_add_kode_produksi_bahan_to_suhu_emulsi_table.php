<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeProduksiBahanToSuhuEmulsiTable extends Migration
{
    public function up()
    {
        Schema::table('suhu_emulsi', function (Blueprint $table) {
            $table->string('kode_produksi_bahan')->nullable()->after('suhu');
        });
    }

    public function down()
    {
        Schema::table('suhu_emulsi', function (Blueprint $table) {
            $table->dropColumn('kode_produksi_bahan');
        });
    }
}