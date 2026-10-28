<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersiapanColdMixing extends Model
{
    use HasFactory;
    protected $table = 'persiapan_cold_mixing';
    protected $fillable = [
        'uuid', 'user_id', 'id_plan', 'shift_id', 'tanggal', 'id_produk', 'id_suhu_adonan', 'rework', 'hasil_pemeriksaan'
    ];
     protected $casts = [
        'tanggal' => 'date',
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function plan() { return $this->belongsTo(Plan::class, 'id_plan'); }
    public function produk() { return $this->belongsTo(JenisProduk::class, 'id_produk'); }
    public function suhu_adonan() { return $this->belongsTo(SuhuAdonan::class, 'id_suhu_adonan'); }
    public function aktuals() { return $this->hasMany(AktualSuhuAdonan::class, 'id_persiapan_cold_mixing'); }
    public function shift() { return $this->belongsTo(DataShift::class, 'shift_id'); }
}
