<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeratProdukBox extends Model
{
    use HasFactory;

    protected $table = 'berat_produk_box';
    protected $guarded = ['id', 'uuid'];
    protected $fillable = [
    'uuid',
    'id_pengemasan_plastik',
    'id_pengemasan_produk',
    'id_shift',
    'id_data_box',
    'line',
    'user_id',
    'tanggal',
    'jam',
    'berat_aktual',
    'rata_rata_berat',
    'id_plan',
    'id_berat_produk_bag',
];
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
           protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'berat_aktual' => 'array',
    ];
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
        return $this->belongsTo(User::class, 'user_id');
    }

   

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function data_box()
    {
        return $this->belongsTo(DataBox::class, 'id_data_box');
    }
    public function pengemasanProduk()
    {
        return $this->belongsTo(PengemasanProduk::class, 'id_pengemasan_produk');
    }
    public function pengemasanPlastik()
    {
        return $this->belongsTo(PengemasanPlastik::class, 'id_pengemasan_plastik');
    }
   public function beratProdukPack()
    {
        return $this->belongsTo(BeratProdukBag::class, 'id_berat_produk_bag');
    }

    public function pengemasanKarton()
    {
        return $this->hasOne(PengemasanKarton::class, 'id_berat_produk_box');
    }
}
