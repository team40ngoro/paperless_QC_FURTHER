<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuhuForming extends Model
{
    protected $table = 'suhu_forming';
    protected $fillable = [
        'uuid',
        'id_persiapan_bahan_forming',
        'id_bahan_forming',
        'suhu',
        'kode_produksi_bahan',
        'sensori',
        'keterangan',
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    public function persiapanBahanForming()
    {
        return $this->belongsTo(PersiapanBahanForming::class, 'id_persiapan_bahan_forming');
    }

    public function bahanForming()
    {
        return $this->belongsTo(BahanForming::class, 'id_bahan_forming');
    }
}
