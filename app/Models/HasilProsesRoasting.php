<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class HasilProsesRoasting extends Model
{
    use HasFactory;

    protected $table = 'hasil_proses_roasting';

    protected $fillable = [
        'uuid',
        'proses_roasting_fan_uuid',
        'input_roasting_uuid',
        'frayer_uuid',
        'frayer2_uuid',
        'frayer3_uuid',
        'frayer4_uuid',
        'frayer5_uuid',
        'breader_uuid',
        'battering_uuid',
        'predust_uuid',
        'penggorengan_uuid',
        'id_plan',
        'user_id',
        'id_shift',
        'id_produk',
        'id_std_suhu_pusat',
        'aktual_suhu_pusat',
        'sensori',
        'keterangan',
        'tanggal',
        'jam'
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'sensori' => 'array',
        'aktual_suhu_pusat' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
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

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    // Relasi ke InputRoasting
    public function inputRoasting()
    {
        return $this->belongsTo(InputRoasting::class, 'input_roasting_uuid', 'uuid');
    }

    // Relasi ke BahanBakuRoasting
    public function bahanBakuRoasting()
    {
        return $this->belongsTo(BahanBakuRoasting::class, 'bahan_baku_roasting_uuid', 'uuid');
    }

    // Relasi ke ProsesRoastingFan
    public function prosesRoastingFan()
    {
        return $this->belongsTo(ProsesRoastingFan::class, 'proses_roasting_fan_uuid', 'uuid');
    }
    public function stdSuhuPusat()
    {
        return $this->belongsTo(StdSuhuPusatRoasting::class, 'id_std_suhu_pusat');
    }
    // Tambahkan accessor untuk mendapatkan shift dari ProsesRoastingFan atau Penggorengan (Dual Flow)
    public function getShiftDataAttribute()
    {
        // KONDISI 1: Cek shift dari Penggorengan (Alur Penggorengan)
        if ($this->penggorengan_uuid) {
            $penggorengan = \App\Models\Penggorengan::where('uuid', $this->penggorengan_uuid)->first();
            if ($penggorengan && $penggorengan->shift) {
                return $penggorengan->shift;
            }
        }
        
        // KONDISI 2: Cek shift dari ProsesRoastingFan (yang sudah punya dual check)
        if ($this->proses_roasting_fan_uuid) {
            $prosesRoastingFan = \App\Models\ProsesRoastingFan::where('uuid', $this->proses_roasting_fan_uuid)->first();
            if ($prosesRoastingFan && $prosesRoastingFan->shift_data) {
                return $prosesRoastingFan->shift_data;
            }
        }
        
        // Fallback ke relationship shift lokal
        return $this->shift;
    }
}
