<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TotalPemakaianEmulsi extends Model
{
    protected $table = 'total_pemakaian_emulsi';
    protected $fillable = [
        'uuid', 'total_pemakaian', 'nama_emulsi_id', 'id_plan', 'user_id', 'id_produk'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function emulsi() {
        return $this->belongsTo(JenisEmulsi::class, 'nama_emulsi_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function plan() {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
    public function produk() {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }
    public function nomorEmulsi() {
        return $this->hasMany(NomorEmulsi::class, 'total_pemakaian_id');
    }
}