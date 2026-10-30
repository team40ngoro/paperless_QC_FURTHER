<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StdSuhuPusatRoasting extends Model
{
    protected $table = 'std_suhu_pusat_roasting';
    
    protected $fillable = [
        'uuid',
        'id_plan',
        'id_produk',
        'std_suhu_pusat_roasting'
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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship to Plan
     */
    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }

    /**
     * Relationship to JenisProduk
     */
    public function produk()
    {
        return $this->belongsTo(\App\Models\JenisProduk::class, 'id_produk');
    }
}
