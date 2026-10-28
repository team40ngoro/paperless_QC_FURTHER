<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuhuEmulsi extends Model
{
    protected $table = 'suhu_emulsi';
    protected $fillable = [
        'bahan_emulsi_id',
        'pembuatan_emulsi_id',
        'suhu',
        'berat_bahan',
        'proses_ke',
        'kode_produksi_bahan',
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function bahanEmulsi()
    {
        return $this->belongsTo(BahanEmulsi::class, 'bahan_emulsi_id', 'id');
    }

    public function pembuatanEmulsi()
    {
        return $this->belongsTo(PembuatanEmulsi::class);
    }
}
