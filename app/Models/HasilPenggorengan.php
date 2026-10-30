<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;

class HasilPenggorengan extends Model
{
    use HasFactory;

    protected $table = 'hasil_penggorengan';
    protected $fillable = [
        'uuid', 'id_plan', 'user_id', 'id_produk', 'id_std_suhu_pusat', 
        'aktual_suhu_pusat', 'sensori_kematangan', 'sensori_kenampakan', 'sensori_warna',
        'sensori_rasa', 'sensori_bau', 'sensori_tekstur', 'tanggal', 'jam', 'frayer_uuid', 'frayer2_uuid', 'frayer3_uuid', 'frayer4_uuid', 'frayer5_uuid',
        'breader_uuid', 'battering_uuid', 'predust_uuid', 'penggorengan_uuid', 'keterangan'
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
    
    public function frayer()
    {
        return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid')
            ->orWhere('uuid', $this->frayer_uuid);
    }
    
    public function breader()
    {
        return $this->belongsTo(ProsesBreader::class, 'breader_uuid', 'uuid');
    }
    
    public function battering()
    {
        return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid');
    }
    
    public function predust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }
    
    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function shift()
    {
        return $this->hasOneThrough(
            DataShift::class,
            Penggorengan::class,
            'uuid', // Foreign key on penggorengan table
            'id', // Foreign key on data_shift table
            'penggorengan_uuid', // Local key on hasil_penggorengan table
            'shift_id' // Local key on penggorengan table
        );
    }

    public function stdSuhuPusat()
    {
        return $this->belongsTo(StdSuhuPusat::class, 'id_std_suhu_pusat', 'id');
    }

    public function frayer2Data()
    {
        return $this->belongsTo(Frayer2::class, 'frayer2_uuid', 'uuid');
    }

    public function frayer3Data()
    {
        return $this->belongsTo(Frayer3::class, 'frayer3_uuid', 'uuid');
    }

    public function frayer4Data()
    {
        return $this->belongsTo(Frayer4::class, 'frayer4_uuid', 'uuid');
    }

    public function frayer5Data()
    {
        return $this->belongsTo(Frayer5::class, 'frayer5_uuid', 'uuid');
    }
}
