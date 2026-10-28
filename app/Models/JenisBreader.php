<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBreader extends Model
{
    protected $table = 'jenis_breader';
    protected $fillable = [
        'uuid', 'id_plan', 'user_id', 'id_produk', 'jenis_breader'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }
}
