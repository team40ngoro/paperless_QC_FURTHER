<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesFrayer extends Model
{
    use HasFactory;

    protected $table = 'proses_frayer';
    protected $fillable = [
        'uuid', 'tanggal', 'jam', 'id_plan', 'id_produk', 'id_suhu_frayer_1', 'id_waktu_penggorengan', 'aktual_penggorengan', 'aktual_suhu_penggorengan', 'tpm_minyak', 'ffa', 'user_id', 'id_shift',
        'penggorengan_uuid', 'predust_uuid', 'battering_uuid', 'breader_uuid', 'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

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

    public function shift()
    {
        return $this->penggorengan ? $this->penggorengan->shift : null;
    }

    public function suhuFrayer()
    {
        return $this->belongsTo(SuhuFrayer1::class, 'id_suhu_frayer_1', 'id');
    }

    public function waktuPenggorengan()
    {
        return $this->belongsTo(WaktuPenggorengan::class, 'id_waktu_penggorengan', 'id');
    }

    // Relasi dengan proses sebelumnya berdasarkan UUID
    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function penggorenganData()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function predustData()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }

    public function batteringData()
    {
        return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid');
    }

    public function breaderData()
    {
        return $this->belongsTo(ProsesBreader::class, 'breader_uuid', 'uuid');
    }

}