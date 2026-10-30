<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Penggorengan extends Model
{
    use HasFactory;

    protected $table = 'penggorengan';
    protected $fillable = [
        'uuid', 'id_produk', 'shift_id', 'id_plan', 'user_id',
        'kode_produksi', 'berat_produk', 'no_of_strokes', 'tanggal', 'jam', 'hasil_pencetakan', 'waktu_pemasakan'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pembuatanPredust()
    {
        return $this->hasMany(PembuatanPredust::class, 'penggorengan_uuid', 'uuid');
    }

    public function prosesBattering()
    {
        return $this->hasMany(ProsesBattering::class, 'penggorengan_uuid', 'uuid');
    }

    public function pembekuanIqfData()
    {
        return $this->hasOne(PembekuanIqfPenggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function pembekuanIqfRoastingData()
    {
        return $this->hasOne(PembekuanIqfRoasting::class, 'penggorengan_uuid', 'uuid');
    }

    public function prosesRoastingFanData()
    {
        return $this->hasMany(ProsesRoastingFan::class, 'penggorengan_uuid', 'uuid');
    }
}
