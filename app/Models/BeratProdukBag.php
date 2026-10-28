<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeratProdukBag extends Model
{
    use HasFactory;

    protected $table = 'berat_produk_bag';
    protected $guarded = ['id'];
    protected $fillable = [
        'uuid',
        'id_plan',
        'id_pengemasan_produk',
        'id_pengemasan_plastik',
        'id_shift',
        'id_data_bag',
        'user_id',
        'line',
        'tanggal',
        'jam',
        'berat_aktual',
        'rata_rata_berat',
    ];

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
    public function pengemasanPlastik()
    {
        return $this->belongsTo(PengemasanPlastik::class, 'id_pengemasan_plastik');
    }

    public function beratProdukBox()
    {
        return $this->hasOne(BeratProdukBox::class, 'id_berat_produk_bag');
    }

    public function data_bag()
    {
        return $this->belongsTo(DataBag::class, 'id_data_bag');
    }
}
