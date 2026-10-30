<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SuhuFrayer2 extends Model
{
    use HasFactory;

    protected $table = 'suhu_frayer_2';

    protected $fillable = [
        'uuid',
        'id_produk',
        'id_plan',
        'user_id',
        'suhu_frayer_2',
        'waktu_penggorengan_2'
    ];

    protected $casts = [
        // 'suhu_frayer_2' => 'decimal:2' // Dihapus karena menyebabkan error cast
    ];

    // Accessor removed as it was causing range strings (e.g. "145 - 155") to return 0
    /*
    public function getSuhuFrayer2Attribute($value)
    {
        if (is_null($value) || $value === '' || !is_numeric($value)) {
            return 0;
        }
        return number_format((float)$value, 2, '.', '');
    }
    */

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
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
}
