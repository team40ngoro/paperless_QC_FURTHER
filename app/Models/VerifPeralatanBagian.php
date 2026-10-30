<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifPeralatanBagian extends Model
{
    use HasFactory;

    protected $table = 'verif_peralatan_bagian';

    protected $fillable = [
        'verif_peralatan_detail_id',
        'id_bagian',
        'verifikasi',
        'keterangan',
        'tindakan_koreksi',
        'verifikasi_hasil',
    ];

    protected $casts = [
        'verifikasi' => 'boolean',
        'verifikasi_hasil' => 'boolean',
    ];

    public function detail()
    {
        return $this->belongsTo(VerifPeralatanDetail::class, 'verif_peralatan_detail_id');
    }

    public function bagian()
    {
        return $this->belongsTo(BagianMesin::class, 'id_bagian');
    }
}
