<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AktualSuhuAdonan extends Model
{
    use HasFactory;
    protected $table = 'aktual_suhu_adonan';
    protected $fillable = [
        'uuid', 'owner_type', 'owner_id', 'id_suhu_adonan',
        'id_persiapan_bahan_forming', 'id_persiapan_bahan_non_forming',
        'aktual_suhu_1', 'aktual_suhu_2', 'aktual_suhu_3', 'aktual_suhu_4', 'aktual_suhu_5', 'total_aktual_suhu'
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    public function owner()
    {
        return $this->morphTo();
    }

    public function suhu_adonan() { return $this->belongsTo(SuhuAdonan::class, 'id_suhu_adonan'); }
}
