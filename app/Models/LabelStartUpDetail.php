<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelStartUpDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'label_start_up_id', 'id_produk', 'berat', 'label_produk', 'jam', 'foto_kode_produksi', 
        'hasil_pengecekan', 'nama_operator', 'ttd_operator', 
        'nama_qc', 'ttd_qc', 'tindakan_koreksi'
    ];

    public function parent()
    {
        return $this->belongsTo(LabelStartUp::class, 'label_start_up_id');
    }

    public function produk()
    {
        return $this->belongsTo(\App\Models\JenisProduk::class, 'id_produk');
    }
}
