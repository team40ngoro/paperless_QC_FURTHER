<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Carbon\Carbon;

class ProsesBreader extends Model
{
    protected $table = 'proses_breader';
    protected $fillable = [
        'uuid', 'battering_uuid', 'predust_uuid', 'penggorengan_uuid', 'id_produk', 'user_id', 'id_plan', 'id_jenis_breader',
        'kode_produksi', 'hasil_breader', 'tanggal', 'jam'
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'kode_produksi' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
   public function jenisBreader()
{
    // Ambil ID pertama jika ada beberapa ID
    $firstId = explode(',', $this->id_jenis_breader)[0] ?? null;
    return $this->belongsTo(JenisBreader::class, 'id_jenis_breader')->where('id', $firstId);
}

// Tambahkan method baru untuk mendapatkan semua jenis breader
public function getAllJenisBreader()
{
    if (!$this->id_jenis_breader) {
        return collect();
    }
    
    $ids = explode(',', $this->id_jenis_breader);
    return JenisBreader::whereIn('id', $ids)->get();
}

// Accessor untuk mendapatkan semua jenis breader sebagai string
public function getJenisBreaderNamesAttribute()
{
    $breaders = $this->getAllJenisBreader();
    return $breaders->pluck('jenis_breader')->implode(', ');
}
    public function shift()
    {
        return $this->hasOneThrough(
            DataShift::class,
            Penggorengan::class,
            'uuid', // Foreign key on penggorengan table
            'id', // Foreign key on data_shift table
            'penggorengan_uuid', // Local key on proses_breader table
            'shift_id' // Local key on penggorengan table
        );
    }

    public function battering()
    {
        return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid');
    }

    public function predust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }

    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function getBreaderNamesWithCodesAttribute()
    {
        $breaders = $this->getAllJenisBreader();
        $codes = is_array($this->kode_produksi) ? $this->kode_produksi : [];
        
        if (empty($codes) && !is_array($this->kode_produksi)) {
            return $this->kode_produksi ?: '-';
        }

        return $breaders->map(function($b) use ($codes) {
            $name = $b->jenis_breader;
            $code = $codes[$b->id] ?? '-';
            return "{$name}: {$code}";
        })->implode(' | ');
    }

    public function getBreaderNamesWithCodesHtmlAttribute()
    {
        $breaders = $this->getAllJenisBreader();
        $codes = is_array($this->kode_produksi) ? $this->kode_produksi : [];
        
        if (empty($codes) && !is_array($this->kode_produksi)) {
            return $this->kode_produksi ?: '-';
        }

        return $breaders->map(function($b) use ($codes) {
            $name = $b->jenis_breader;
            $code = $codes[$b->id] ?? '-';
            return "<strong>{$name}</strong>: {$code}";
        })->implode('<br>');
    }
}
