<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WaktuPenggorengan2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $waktuData = [
            // Untuk Suhu Frayer 2 ID 1 (170°C) - Produk 1
            ['id_suhu_frayer_2' => 1, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 120],
            ['id_suhu_frayer_2' => 1, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 150],
            ['id_suhu_frayer_2' => 1, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 180],
            
            // Untuk Suhu Frayer 2 ID 2 (175°C) - Produk 1
            ['id_suhu_frayer_2' => 2, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 110],
            ['id_suhu_frayer_2' => 2, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 130],
            ['id_suhu_frayer_2' => 2, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 160],
            
            // Untuk Suhu Frayer 2 ID 3 (180°C) - Produk 1
            ['id_suhu_frayer_2' => 3, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 100],
            ['id_suhu_frayer_2' => 3, 'id_produk' => 1, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 120],
            ['id_suhu_frayer_2' => 3, 'id_produk' => 1, 'id_plan' => 1, 'waktu_penggorengan_2' => 140],
            
            // Untuk Suhu Frayer 2 ID 4-9 (produk lainnya)
            ['id_suhu_frayer_2' => 4, 'id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 130],
            ['id_suhu_frayer_2' => 4, 'id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 150],
            ['id_suhu_frayer_2' => 5, 'id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 125],
            ['id_suhu_frayer_2' => 5, 'id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 145],
            ['id_suhu_frayer_2' => 6, 'id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 135],
            ['id_suhu_frayer_2' => 6, 'id_produk' => 2, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 155],
            ['id_suhu_frayer_2' => 7, 'id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 115],
            ['id_suhu_frayer_2' => 7, 'id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 135],
            ['id_suhu_frayer_2' => 8, 'id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 125],
            ['id_suhu_frayer_2' => 8, 'id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 145],
            ['id_suhu_frayer_2' => 9, 'id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 140],
            ['id_suhu_frayer_2' => 9, 'id_produk' => 3, 'id_plan' => 1, 'user_id' => 1, 'waktu_penggorengan_2' => 160],
        ];

        foreach ($waktuData as $data) {
            \App\Models\WaktuPenggorengan2::create($data);
        }
    }
}
