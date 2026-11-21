<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PengemasanProduk extends Model
{
    use HasFactory;

    protected $table = 'pengemasan_produk';

    protected $fillable = [
        'uuid',
        'id_plan',
        'id_produk',
        'id_shift',
        'tanggal',
        'jam',
        'user_id',
        'kode_form',
        'berat',
        'tanggal_expired',
        'kode_produksi',
        'is_kombinasi',
        'id_produk_2',
        'kode_produksi_2',
        'berat_produk_2',
        'tanggal_expired_2',
        'std_suhu_produk_iqf',
        'suhu_iqf',
        'aktual_suhu_produk',
        'waktu_awal_packing',
        'status_release_produk',
        'verifikasi_akhir_pengemasan',
        'tindakan_koreksi',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'tanggal_expired' => 'datetime',
        'tanggal_expired_2' => 'datetime',
        'is_kombinasi' => 'boolean',
    ];

    public function getAktualSuhuProdukAttribute($value)
    {
        if (empty($value)) return $value;

        if (is_string($value) && str_starts_with(trim($value), '[')) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $result = [];
                for ($i = 0; $i < count($decoded); $i++) {
                    $curr = trim($decoded[$i]);
                    if (isset($decoded[$i + 1])) {
                        $next = trim($decoded[$i + 1]);
                        // Pasangkan angka negatif dan positif (misal "-19" dan "3" jadi "-19.3")
                        if (is_numeric($curr) && is_numeric($next) && $curr < 0 && $next >= 0 && strpos($next, '-') === false && strpos($curr, '.') === false) {
                            $result[] = $curr . '.' . $next;
                            $i++; // skip next
                            continue;
                        }
                    }
                    $result[] = $curr;
                }
                return implode(', ', $result);
            }
        }
        
        if (is_string($value)) {
            $value = str_replace('/', ',', $value);
        }

        return $value;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function produk2()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk_2');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function pengemasanPlastik()
    {
        return $this->hasOne(PengemasanPlastik::class, 'id_pengemasan_produk');
    }
}
