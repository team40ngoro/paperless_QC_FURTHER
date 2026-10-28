<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WaktuPenggorengan2 extends Model
{
    use HasFactory;

    protected $table = 'waktu_penggorengan_2';

    protected $fillable = [
        'uuid',
        'id_produk',
        'id_plan',
        'user_id',
        'id_suhu_frayer_2',
        'waktu_penggorengan_2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suhuFrayer2()
    {
        return $this->belongsTo(SuhuFrayer2::class, 'id_suhu_frayer_2');
    }
}
