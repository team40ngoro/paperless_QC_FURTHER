<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JenisProduk extends Model
{
    protected $table = 'jenis_produk';
    protected $fillable = [
        'uuid',
        'id_plan',
        'nama_produk',
        'user_id',
        'status_bahan',
        'id_produk_rework',
    ];

    protected $casts = [
        'id_produk_rework' => 'array',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suhuBlok()
    {
        return $this->hasMany(SuhuBlok::class, 'id_produk');
    }
}
