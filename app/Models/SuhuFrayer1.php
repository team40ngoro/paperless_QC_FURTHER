<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuhuFrayer1 extends Model
{
    protected $table = 'suhu_frayer_1';
    protected $fillable = [
    'uuid', 'user_id', 'id_produk', 'id_plan', 
    'suhu_frayer',        'waktu_penggorengan_1',
    'suhu_frayer_2',      'waktu_penggorengan_2',
    'suhu_frayer_3',      'waktu_penggorengan_3',
    'suhu_frayer_4',      'waktu_penggorengan_4',
    'suhu_frayer_5',      'waktu_penggorengan_5',
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
}
