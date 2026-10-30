<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WaktuPenggorengan extends Model
{
    use HasFactory;

    protected $table = 'waktu_penggorengan';
    protected $fillable = [
        'uuid', 'user_id', 'id_produk', 'id_plan', 'id_suhu_frayer_1', 'waktu_penggorengan'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
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
    public function suhuFrayer()
    {
        return $this->belongsTo(SuhuFrayer1::class, 'id_suhu_frayer_1');
    }
}
