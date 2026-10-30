<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Frayer4 extends Model
{
    use HasFactory;

    protected $table = 'frayer_4';

    protected $fillable = [
        'uuid',
        'id_produk',
        'id_plan',
        'user_id',
        'id_suhu_frayer',
        'id_waktu_penggorengan',
        'aktual_penggorengan',
        'aktual_suhu_penggorengan',
        'tpm_minyak',
        'tanggal',
        'jam',
        'penggorengan_uuid',
        'predust_uuid',
        'battering_uuid',
        'breader_uuid',
        'keterangan'
    ];

    protected $dates = ['tanggal'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suhuFrayer()
    {
        return $this->belongsTo(SuhuFrayer1::class, 'id_suhu_frayer');
    }

    public function waktuPenggorengan()
    {
        return $this->belongsTo(WaktuPenggorengan::class, 'id_waktu_penggorengan');
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
