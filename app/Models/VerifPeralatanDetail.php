<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifPeralatanDetail extends Model
{
    use HasFactory;

    protected $table = 'verif_peralatan_detail';

    protected $fillable = [
        'verif_peralatan_id',
        'id_mesin',
        'id_area',
        'jam',
        'verifikasi',
        'keterangan',
        'tindakan_koreksi',
        'verifikasi_hasil',
    ];

    protected $casts = [
        'verifikasi' => 'boolean',
        'verifikasi_hasil' => 'boolean',
    ];

    public function verifPeralatan()
    {
        return $this->belongsTo(VerifPeralatan::class, 'verif_peralatan_id');
    }

    public function mesin()
    {
        return $this->belongsTo(InputMesinPeralatan::class, 'id_mesin');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function bagianDetails()
    {
        return $this->hasMany(VerifPeralatanBagian::class, 'verif_peralatan_detail_id');
    }
}
