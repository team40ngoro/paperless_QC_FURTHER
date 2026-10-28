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
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            // Add new JSON columns for block data
            $table->json('blok_data')->nullable()->after('id_produk');
            $table->json('suhu_blok_ids')->nullable()->after('blok_data');
            $table->json('std_fan_ids')->nullable()->after('suhu_blok_ids');
            
            // Remove old individual block columns
            $table->dropColumn([
                'id_suhu_blok',
                'id_std_fan',
                'suhu_roasting',
                'fan_1',
                'fan_2',
                'fan_3',
                'fan_4',
                'aktual_humadity',
                'infra_red',
                'conveyor_bandung',
                'conveyor_infeed',
                'conveyor_outfeed',
                'conveyor_blok1',
                'block_number'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_roasting_fan', function (Blueprint $table) {
            // Add back old columns
            $table->unsignedBigInteger('id_suhu_blok')->nullable()->after('id_produk');
            $table->unsignedBigInteger('id_std_fan')->nullable()->after('id_suhu_blok');
            $table->decimal('suhu_roasting', 8, 2)->nullable()->after('id_std_fan');
            $table->decimal('fan_1', 8, 2)->nullable()->after('suhu_roasting');
            $table->decimal('fan_2', 8, 2)->nullable()->after('fan_1');
            $table->decimal('fan_3', 8, 2)->nullable()->after('fan_2');
            $table->decimal('fan_4', 8, 2)->nullable()->after('fan_3');
            $table->decimal('aktual_humadity', 8, 2)->nullable()->after('fan_4');
            $table->string('infra_red')->nullable()->after('aktual_humadity');
            $table->string('conveyor_bandung')->nullable()->after('infra_red');
            $table->string('conveyor_infeed')->nullable()->after('conveyor_bandung');
            $table->string('conveyor_outfeed')->nullable()->after('conveyor_infeed');
            $table->string('conveyor_blok1')->nullable()->after('conveyor_outfeed');
            $table->string('block_number')->nullable()->after('conveyor_blok1');
            
            // Remove new JSON columns
            $table->dropColumn(['blok_data', 'suhu_blok_ids', 'std_fan_ids']);
        });
    }
};
