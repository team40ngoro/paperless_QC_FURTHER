<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plan;
use App\Models\Produk;
use App\Models\NamaEmulsi;

class NomorEmulsi extends Model
{
    use HasFactory;

    protected $table = 'nomor_emulsi';
    protected $fillable = [
        'uuid', 'nomor_emulsi', 'total_pemakaian_id', 'id_produk', 'nama_emulsi_id', 'id_plan', 'user_id'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }

    public function produk()
    {
        return $this->belongsTo(\App\Models\JenisProduk::class, 'id_produk');
    }

    public function total_pemakaian()
    {
        return $this->belongsTo(\App\Models\TotalPemakaianEmulsi::class, 'total_pemakaian_id');
    }

    public function emulsi()
    {
        return $this->belongsTo(\App\Models\JenisEmulsi::class, 'nama_emulsi_id');
    }

    public function bahanEmulsi()
    {
        return $this->hasMany(\App\Models\BahanEmulsi::class, 'nomor_emulsi_id');
    }
}
