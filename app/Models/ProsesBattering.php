<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProsesBattering extends Model
{
    protected $table = 'proses_battering';
    protected $fillable = [
        'uuid', 'id_plan', 'user_id', 'penggorengan_uuid', 'predust_uuid', 'id_produk', 'kode_produksi_better', 'id_jenis_better', 'tanggal', 'jam', 'hasil_better'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'kode_produksi_better' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan() { return $this->belongsTo(Plan::class, 'id_plan'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function produk() { return $this->belongsTo(JenisProduk::class, 'id_produk'); }
    public function jenis_better()
    {
        // Ambil ID pertama jika ada beberapa ID untuk kompatibilitas basic
        $firstId = explode(',', $this->id_jenis_better)[0] ?? null;
        return $this->belongsTo(JenisBetter::class, 'id_jenis_better')->where('id', $firstId);
    }

    // Method baru untuk mendapatkan semua jenis better
    public function getAllJenisBetter()
    {
        if (!$this->id_jenis_better) {
            return collect();
        }
        
        $ids = explode(',', $this->id_jenis_better);
        return JenisBetter::whereIn('id', $ids)->get();
    }

    // Accessor untuk mendapatkan semua jenis better sebagai string
    public function getJenisBetterNamesAttribute()
    {
        $betters = $this->getAllJenisBetter();
        return $betters->map(function($b) {
            return $b->nama_formula_better ?: $b->nama_better;
        })->implode(', ');
    }

    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function predust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }

    public function getBetterNamesWithCodesAttribute()
    {
        $betters = $this->getAllJenisBetter();
        $codes = is_array($this->kode_produksi_better) ? $this->kode_produksi_better : [];
        
        if (empty($codes) && !is_array($this->kode_produksi_better)) {
            return $this->kode_produksi_better ?: '-';
        }

        return $betters->map(function($b) use ($codes) {
            $name = $b->nama_formula_better ?: $b->nama_better;
            $code = $codes[$b->id] ?? '-';
            return "{$name}: {$code}";
        })->implode(' | ');
    }

    public function getBetterNamesWithCodesHtmlAttribute()
    {
        $betters = $this->getAllJenisBetter();
        $codes = is_array($this->kode_produksi_better) ? $this->kode_produksi_better : [];
        
        if (empty($codes) && !is_array($this->kode_produksi_better)) {
            return $this->kode_produksi_better ?: '-';
        }

        return $betters->map(function($b) use ($codes) {
            $name = $b->nama_formula_better ?: $b->nama_better;
            $code = $codes[$b->id] ?? '-';
            return "<strong>{$name}</strong>: {$code}";
        })->implode('<br>');
    }
}