<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\DataRm;
use App\Models\ProsesTwahing;

class ProsesTwahingDetail extends Model
{
    use HasFactory;

    protected $table = 'proses_twahing_detail';

    protected $fillable = [
        'uuid',
        'proses_twahing_id',
        'id_rm',
        'kode_produksi',
        'kondisi_ruang',
        'waktu_pemeriksaan',
        'suhu_rm',
        'suhu_ruang',
        'suhu_air_thawing',
        'suhu_produk',
        'kondisi_produk',
    ];

    protected $casts = [
        'suhu_rm' => 'decimal:2',
        'suhu_ruang' => 'decimal:2',
        'suhu_air_thawing' => 'decimal:2',
        'suhu_produk' => 'decimal:2',
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

    public function header()
    {
        return $this->belongsTo(ProsesTwahing::class, 'proses_twahing_id');
    }

    public function rm()
    {
        return $this->belongsTo(DataRm::class, 'id_rm');
    }
}
