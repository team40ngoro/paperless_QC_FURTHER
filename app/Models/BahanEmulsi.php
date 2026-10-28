<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanEmulsi extends Model
{
    protected $table = 'bahan_emulsi';
    protected $fillable = [
        'uuid', 'id_plan', 'id_produk', 'nama_emulsi_id', 
        'total_pemakaian_id', 'nomor_emulsi_id', 
        'nama_rm', 'berat_rm', 'user_id'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    public function nomor_emulsi() {
        return $this->belongsTo(\App\Models\NomorEmulsi::class, 'nomor_emulsi_id');
    }
    public function total_pemakaian() {
        return $this->belongsTo(\App\Models\TotalPemakaianEmulsi::class, 'total_pemakaian_id');
    }
    public function produk() {
        return $this->belongsTo(\App\Models\JenisProduk::class, 'id_produk');
    }
    public function emulsi() {
        return $this->belongsTo(\App\Models\JenisEmulsi::class, 'nama_emulsi_id');
    }
    public function plan() {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }
    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function suhuEmulsi()
    {
        return $this->hasMany(SuhuEmulsi::class);
    }
}
