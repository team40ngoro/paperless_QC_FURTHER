<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produk_forming', function (Blueprint $table) {
            // Add the new JSON column for mesin & peralatan
            $table->json('mesin_peralatan')->nullable()->after('labelisasi_karton');
        });

        // Migrate existing individual machine data into the new JSON column
        // using raw SQL for efficiency
        \Illuminate\Support\Facades\DB::statement("
            UPDATE produk_forming
            SET mesin_peralatan = JSON_ARRAY(
                IF(autogrind IS NOT NULL, JSON_OBJECT('nama_mesin','Autogrind','kondisi', autogrind, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(bowlcutter IS NOT NULL, JSON_OBJECT('nama_mesin','Bowlcutter','kondisi', bowlcutter, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(ayakan_seasoning IS NOT NULL, JSON_OBJECT('nama_mesin','Ayakan Seasoning','kondisi', ayakan_seasoning, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(unimix IS NOT NULL, JSON_OBJECT('nama_mesin','Unimix','kondisi', unimix, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(revoformer IS NOT NULL, JSON_OBJECT('nama_mesin','Revoformer','kondisi', revoformer, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(better_mixer IS NOT NULL, JSON_OBJECT('nama_mesin','Better Mixer','kondisi', better_mixer, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(wet_coater IS NOT NULL, JSON_OBJECT('nama_mesin','Wet Coater','kondisi', wet_coater, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(breader IS NOT NULL, JSON_OBJECT('nama_mesin','Breader','kondisi', breader, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(frayer_1 IS NOT NULL, JSON_OBJECT('nama_mesin','Frayer 1','kondisi', frayer_1, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(frayer_2 IS NOT NULL, JSON_OBJECT('nama_mesin','Frayer 2','kondisi', frayer_2, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(iqf_jbt IS NOT NULL, JSON_OBJECT('nama_mesin','IQF JBT','kondisi', iqf_jbt, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(keranjang IS NOT NULL, JSON_OBJECT('nama_mesin','Keranjang','kondisi', keranjang, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(timbangan IS NOT NULL, JSON_OBJECT('nama_mesin','Timbangan','kondisi', timbangan, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(mhw IS NOT NULL, JSON_OBJECT('nama_mesin','MHW','kondisi', mhw, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(foot_sealer IS NOT NULL, JSON_OBJECT('nama_mesin','Foot Sealer','kondisi', foot_sealer, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(metal_detector IS NOT NULL, JSON_OBJECT('nama_mesin','Metal Detector','kondisi', metal_detector, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(rotary_table IS NOT NULL, JSON_OBJECT('nama_mesin','Rotary Table','kondisi', rotary_table, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(carton_sealer IS NOT NULL, JSON_OBJECT('nama_mesin','Carton Sealer','kondisi', carton_sealer, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(meatcar IS NOT NULL, JSON_OBJECT('nama_mesin','Meatcar','kondisi', meatcar, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(check_weigher_bag IS NOT NULL, JSON_OBJECT('nama_mesin','Check Weigher Bag','kondisi', check_weigher_bag, 'tindakan_koreksi','','verifikasi',''), NULL),
                IF(check_weigher_box IS NOT NULL, JSON_OBJECT('nama_mesin','Check Weigher Box','kondisi', check_weigher_box, 'tindakan_koreksi','','verifikasi',''), NULL)
            )
            WHERE (autogrind IS NOT NULL OR bowlcutter IS NOT NULL OR ayakan_seasoning IS NOT NULL)
        ");
    }

    public function down()
    {
        Schema::table('produk_forming', function (Blueprint $table) {
            $table->dropColumn('mesin_peralatan');
        });
    }
};
