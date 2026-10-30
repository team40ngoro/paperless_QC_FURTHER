<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use App\Models\StdSuhuPusatRoasting;

class ProsesRoastingFan extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    protected $table = 'proses_roasting_fan';

    protected $fillable = [
        'id_plan',
        'user_id',
        'id_shift',
        'tanggal',
        'id_produk',
        'blok_data', // JSON field untuk menyimpan data semua blok
        'is_grouped', // Flag untuk menandai apakah data sudah digabung
        'aktual_lama_proses',
        'jam',
        'waktu_pemasakan',
        'aktual_waktu',
        'aktual_suhu_fan',
        'aktual_speed_fan',
        // UUID fields untuk relasi ke proses sebelumnya
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
        // Keep old fields for backward compatibility
        'id_suhu_blok',
        'id_std_fan',
        'suhu_roasting',
        'fan_1',
        'fan_2',
        'fan_3',
        'fan_4',
        'aktual_humadity',
        'infra_red',
        'conveyor_bandung',
        'conveyor_infeed',
        'conveyor_outfeed',
        'conveyor_blok1',
        'block_number',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'blok_data' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function suhuBlok()
    {
        return $this->belongsTo(SuhuBlok::class, 'id_suhu_blok');
    }

    public function stdFan()
    {
        return $this->belongsTo(StdFan::class, 'id_std_fan');
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

    // Relasi ke proses sebelumnya
    public function frayer()
    {
        return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid');
    }

    public function frayer2()
    {
        return $this->belongsTo(Frayer2::class, 'frayer2_uuid', 'uuid');
    }

    public function frayer3()
    {
        return $this->belongsTo(Frayer3::class, 'frayer3_uuid', 'uuid');
    }

    public function frayer4()
    {
        return $this->belongsTo(Frayer4::class, 'frayer4_uuid', 'uuid');
    }

    public function frayer5()
    {
        return $this->belongsTo(Frayer5::class, 'frayer5_uuid', 'uuid');
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

    // Relasi ke Input Roasting dan Bahan Baku Roasting
    public function inputRoasting()
    {
        return $this->belongsTo(InputRoasting::class, 'input_roasting_uuid', 'uuid');
    }

    public function stdSuhuPusatRoasting()
    {
        return $this->belongsTo(StdSuhuPusatRoasting::class, 'std_suhu_pusat_roasting_uuid', 'uuid');
    }

    public function bahanBakuRoasting()
    {
        return $this->belongsTo(BahanBakuRoasting::class, 'bahan_baku_roasting_uuid', 'uuid');
    }
    // Tambahkan accessor untuk mendapatkan shift dari InputRoasting atau Penggorengan (Dual Flow)
    public function getShiftDataAttribute()
    {
        // KONDISI 1: Cek shift dari Penggorengan (Alur Penggorengan)
        if ($this->penggorengan_uuid) {
            $penggorengan = \App\Models\Penggorengan::where('uuid', $this->penggorengan_uuid)->first();
            if ($penggorengan && $penggorengan->shift) {
                return $penggorengan->shift;
            }
        }
        
        // KONDISI 2: Cek shift dari Input Roasting (Alur Input Roasting)
        if ($this->input_roasting_uuid) {
            $inputRoasting = \App\Models\InputRoasting::where('uuid', $this->input_roasting_uuid)->first();
            if ($inputRoasting && $inputRoasting->shift) {
                return $inputRoasting->shift;
            }
        }
        
        // Fallback ke relationship shift lokal
        return $this->shift;
    }
}
