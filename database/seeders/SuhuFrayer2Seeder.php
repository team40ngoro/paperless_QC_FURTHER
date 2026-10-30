<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuhuFrayer2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suhuData = [
            // Produk ID 1 - contoh suhu untuk Frayer 2
            ['id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 170.00],
            ['id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 175.00],
            ['id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 180.00],
            
            // Produk ID 2
            ['id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 165.00],
            ['id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 170.00],
            ['id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 175.00],
            
            // Produk ID 3
            ['id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 175.00],
            ['id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 180.00],
            ['id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'suhu_frayer_2' => 185.00],
        ];

        foreach ($suhuData as $data) {
            \App\Models\SuhuFrayer2::create($data);
        }
    }
}
