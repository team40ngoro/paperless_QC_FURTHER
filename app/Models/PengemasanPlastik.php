<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengemasanPlastik extends Model
{
    use HasFactory;
    protected $table = 'pengemasan_plastik';

    protected $fillable = [
        'uuid',
        //'id_produk',
        'user_id',
        'id_pengemasan_produk',
        'berat',
        'jam',
        'id_plan',
        'id_shift',
        'tanggal',
        'proses_penimbangan',
        'proses_sealing',
        'identitas_produk',
        'nomor_md',
        'kode_kemasan_plastik',
        'line',
        'waktu_pengemasan',
        'kesesuaian_label_plastik',
        'kekuatan_seal',
    ];
       protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
    ];
     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function produk()
    // {
    //     return $this->belongsTo(JenisProduk::class, 'id_produk');
    // }
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');    
    }
    public function pengemasanProduk()
    {
        return $this->belongsTo(PengemasanProduk::class, 'id_pengemasan_produk');
    }

    public function beratProdukBag()
    {
        return $this->hasMany(BeratProdukBag::class, 'id_pengemasan_plastik');
    }
}
