<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PersiapanBahanNonFormingDetail extends Model
{
    protected $table = 'persiapan_bahan_non_forming_detail';

    protected $fillable = [
        'uuid',
        'id_persiapan_bahan_non_forming',
        'id_bahan_non_forming',
        'suhu',
        'kode_produksi_bahan',
        'sensori',
        'keterangan',
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

    public function persiapan()
    {
        return $this->belongsTo(PersiapanBahanNonForming::class, 'id_persiapan_bahan_non_forming');
    }

    public function bahanNonForming()
    {
        return $this->belongsTo(BahanNonForming::class, 'id_bahan_non_forming');
    }
}
