<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VerifikasiProsesTumbling extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_proses_tumbling';

    protected $fillable = [
        'uuid',
        'id_plan',
        'bahan_baku_tumbling_id',
        'bahan_baku_tumbling_uuid',
        'proses_aging_id',
        'proses_aging_uuid',
        'verifikasi_bahan_baku',
        'verifikasi_hasil_pencampuran',
        'suhu_bahan_baku',
        'kondisi_bahan_baku',
        'verifikasi_proses_tumbling',
        'suhu_hasil_tumbling',
        'sensori_hasil_tumbling',
        'aging_data',
        'waktu_selesai_aging',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'aging_data' => 'array',
        'verified_at' => 'datetime',
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function bahanBakuTumbling()
    {
        return $this->belongsTo(BahanBakuTumbling::class, 'bahan_baku_tumbling_id');
    }

    public function prosesAging()
    {
        return $this->belongsTo(ProsesAging::class, 'proses_aging_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
