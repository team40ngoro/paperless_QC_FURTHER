<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BahanBakuTumbling extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku_tumbling';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'id_produk',
        'id_bahan_nonforming',
        'kode_produksi',
        'kode_form',
        'nama_bahan_baku',
        'kode_produksi_bahan_baku',
        'jumlah',
        'suhu',
        'jam',
        'kondisi_daging',
        'salinity',
        'viskositas',
        'gramase',
        'hasil_pencampuran',
        'keterangan',
        'manual_bahan_data',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'manual_bahan_data' => 'json',
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
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    // Relasi balik ke Proses Marinade (one-to-many)
    public function prosesMarinades()
    {
        return $this->hasMany(ProsesMarinadeModel::class, 'bahan_baku_tumbling_id');
    }

    // Relasi balik ke Proses Tumbling (one-to-many)
    public function prosesTumblings()
    {
        return $this->hasMany(ProsesTumbling::class, 'bahan_baku_tumbling_id');
    }

    public function prosesAging()
    {
        return $this->hasOneThrough(
            ProsesAging::class,
            ProsesTumbling::class,
            'bahan_baku_tumbling_uuid', // Foreign key on proses_tumbling table
            'proses_tumbling_uuid',     // Foreign key on proses_aging table
            'uuid',                      // Local key on bahan_baku_tumbling table
            'uuid'                       // Local key on proses_tumbling table
        );
    }
}