<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_speed VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_vakum VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_speed_non_vakum VARCHAR(255) NULL");
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_tekanan_non_vakum VARCHAR(255) NULL");
    }

    public function down()
    {
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_speed DECIMAL(10,2) NULL");
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_vakum DECIMAL(10,2) NULL");
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_speed_non_vakum DECIMAL(10,2) NULL");
        \DB::statement("ALTER TABLE proses_tumbling MODIFY aktual_tekanan_non_vakum DECIMAL(10,2) NULL");
    }
};
