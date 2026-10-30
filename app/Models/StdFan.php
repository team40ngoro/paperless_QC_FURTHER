<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StdFan extends Model
{
    use HasFactory;

    protected $table = 'std_fan';

    protected $fillable = [
        'uuid',
        'id_produk',
        'id_plan',
        'user_id',
        'id_suhu_blok',
        'std_fan',
        'std_fan_2',
        'std_lama_proses',
        'fan_3',
        'fan_4',
        'std_humadity'
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suhuBlok()
    {
        return $this->belongsTo(SuhuBlok::class, 'id_suhu_blok');
    }
}
