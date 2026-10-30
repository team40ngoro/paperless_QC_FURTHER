<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Frayer2 extends Model
{
    use HasFactory;

    protected $table = 'frayer_2';
    protected $fillable = [
        'uuid', 'id_plan', 'tanggal', 'user_id', 'id_produk', 'id_suhu_frayer_2', 'id_waktu_penggorengan_2','jam', 'aktual_penggorengan', 'aktual_suhu_penggorengan', 'tpm_minyak', 'ffa', 'frayer_uuid', 'penggorengan_uuid', 'predust_uuid', 'battering_uuid', 'breader_uuid', 'keterangan'
    ];

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
        return $this->belongsTo(Plan::class, 'id_plan', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk', 'id');
    }

    public function suhuFrayer()
    {
        return $this->belongsTo(SuhuFrayer1::class, 'id_suhu_frayer_2', 'id');
    }

    public function suhuFrayer2()
    {
        return $this->belongsTo(SuhuFrayer1::class, 'id_suhu_frayer_2', 'id');
    }

    public function waktuPenggorengan()
    {
        return $this->belongsTo(WaktuPenggorengan2::class, 'id_waktu_penggorengan_2', 'id');
    }

    public function waktuPenggorengan2()
    {
        return $this->belongsTo(WaktuPenggorengan2::class, 'id_waktu_penggorengan_2', 'id');
    }

    // Relasi dengan proses frayer sebelumnya berdasarkan UUID
    public function frayerData()
    {
        return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid');
    }

    public function penggorenganData()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    // Relasi ke penggorengan melalui frayer
    public function penggorengan()
    {
        if ($this->penggorengan_uuid) {
            return $this->penggorenganData();
        }

        return $this->hasOneThrough(
            Penggorengan::class,
            ProsesFrayer::class,
            'uuid', // Foreign key on ProsesFrayer table
            'uuid', // Foreign key on Penggorengan table
            'frayer_uuid', // Local key on Frayer2 table
            'penggorengan_uuid' // Local key on ProsesFrayer table
        );
    }
}
