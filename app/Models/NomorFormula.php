<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Plan;
use App\Models\JenisProduk;

class NomorFormula extends Model
{
    protected $table = 'nomor_formula';
    protected $fillable = [
        'uuid',
        'id_plan',
        'id_produk',
        'nomor_formula',
        'user_id',
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
        return $this->belongsTo(User::class);
    }

    public function bahanForming()
    {
        return $this->hasMany(BahanForming::class, 'id_formula');
    }
}
