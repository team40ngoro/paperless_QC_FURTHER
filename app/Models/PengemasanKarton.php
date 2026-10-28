<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengemasanKarton extends Model
{
    use HasFactory;

    protected $table = 'pengemasan_karton';
   
    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
         'jam', 
        'id_berat_produk_box',
        'id_berat_produk_bag',
        'id_pengemasan_plastik',
        'id_pengemasan_produk',
        'identitas_produk_pada_karton',
        'standar_jumlah_karton',
        'aktual_jumlah_karton',
        'kesesuaian_isi_pack',
        'keterangan',
    ];
    
    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'kesesuaian_isi_pack' => 'array',
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

    // Relasi ke Plan
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    // Relasi ke JenisProduk
    // public function produk()
    // {
    //     return $this->belongsTo(JenisProduk::class, 'id_produk');
    // }
    public function beratProdukBox()
    {
        return $this->belongsTo(BeratProdukBox::class, 'id_berat_produk_box');
    }
    public function beratProdukBag()
    {
        return $this->belongsTo(BeratProdukBag::class, 'id_berat_produk_bag');
    }
    public function pengemasanPlastik()
    {
        return $this->belongsTo(PengemasanPlastik::class, 'id_pengemasan_plastik');
    }
    public function pengemasanProduk()
    {
        return $this->belongsTo(PengemasanProduk::class, 'id_pengemasan_produk');
    }

    public function dokumentasi()
    {
        return $this->hasOne(Dokumentasi::class, 'id_pengemasan_karton');
    }
}
