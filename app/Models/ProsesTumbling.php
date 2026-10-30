<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\DataShift; // tambahkan import DataShift
use App\Models\BahanBakuTumbling; // tambahkan import BahanBakuTumbling
use App\Models\ProsesMarinadeModel; // tambahkan import ProsesMarinadeModel

class ProsesTumbling extends Model
{
    use HasFactory;

    protected $table = 'proses_tumbling';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'bahan_baku_tumbling_uuid',
        'bahan_baku_tumbling_id',
        'proses_marinade_uuid',
        'proses_marinade_id',
        'id_plan',
        'user_id',
        'id_produk',
        'id_tumbling',
        'shift_id',
        'aktual_drum_on',
        'aktual_drum_off',
        'aktual_speed',
        'aktual_total_waktu',
        'aktual_vakum',
        'aktual_drum_on_non_vakum',
        'aktual_drum_off_non_vakum',
        'aktual_speed_non_vakum',
        'aktual_total_waktu_non_vakum',
        'aktual_tekanan_non_vakum',
        'waktu_mulai_tumbling',
        'waktu_selesai_tumbling',
        'waktu_mulai_tumbling_non_vakum',
        'waktu_selesai_tumbling_non_vakum',
        'suhu_hasil_tumbling',
        'sensori_hasil_tumbling',
        // 'suhu',
        // 'kondisi',
        // 'waktu_mulai_aging',
        // 'waktu_selesai_aging',
        'kode_produksi',
        'kode_form',
        'tanggal',
        'jam'
    ];

    protected $casts = [
        'is_audit' => 'boolean',
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'aktual_speed' => 'string',
        'aktual_vakum' => 'string',
        'aktual_speed_non_vakum' => 'string',
        'aktual_tekanan_non_vakum' => 'string',
        'suhu' => 'string'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
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

    public function dataTumbling()
    {
        return $this->belongsTo(DataTumbling::class, 'id_tumbling');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function bahanBakuTumbling()
    {
        return $this->belongsTo(BahanBakuTumbling::class, 'bahan_baku_tumbling_id');
    }

    public function prosesMarinade()
    {
        return $this->belongsTo(ProsesMarinadeModel::class, 'proses_marinade_id');
    }
    public function prosesAging()
    {
        return $this->hasOne(ProsesAging::class, 'proses_tumbling_uuid', 'uuid');
    }
}
