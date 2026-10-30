<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanProdukCookingMixerFlaLog extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_produk_cooking_mixer_fla_logs';

    protected $fillable = [
        'uuid',
        'pemeriksaan_produk_cooking_mixer_fla_id',
        'pemeriksaan_produk_cooking_mixer_fla_uuid',
        'user_id',
        'user_name',
        'aksi',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'ip_address',
        'user_agent',
        'keterangan'
    ];

    protected $casts = [
        'field_yang_diubah' => 'array',
        'nilai_lama' => 'array',
        'nilai_baru' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relasi ke PemeriksaanProdukCookingMixerFla
    public function pemeriksaanProdukCookingMixerFla()
    {
        return $this->belongsTo(PemeriksaanProdukCookingMixerFla::class, 'pemeriksaan_produk_cooking_mixer_fla_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Method untuk mendapatkan nama field yang user-friendly
    public static function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'id_nama_formula_fla' => 'Formula FLA',
            'kode_produksi_bahan' => 'Kode Produksi Bahan',
            'berat' => 'Berat',
            'kode_produksi' => 'Kode Produksi',
            'waktu_start' => 'Waktu Start',
            'waktu_stop' => 'Waktu Stop',
            'status_gas' => 'Status Gas',
            'speed' => 'Speed',
            'suhu_awal' => 'Suhu Awal',
            'suhu_akhir' => 'Suhu Akhir',
            'keterangan' => 'Keterangan',
            'user_id' => 'User'
        ];
    }

    // Method untuk mendapatkan nama field yang readable
    public function getNamaFieldAttribute()
    {
        if (!$this->field_yang_diubah) {
            return 'N/A';
        }
        
        $fieldNames = self::getFieldNames();
        $namaField = [];
        
        foreach ($this->field_yang_diubah as $field) {
            $namaField[] = $fieldNames[$field] ?? $field;
        }
        
        return implode(', ', $namaField);
    }

    // Method untuk mendapatkan deskripsi perubahan
    public function getDeskripsiPerubahanAttribute()
    {
        if (!$this->field_yang_diubah || !$this->nilai_lama || !$this->nilai_baru) {
            return 'N/A';
        }
        
        $fieldNames = self::getFieldNames();
        $deskripsi = [];
        
        foreach ($this->field_yang_diubah as $field) {
            $namaField = $fieldNames[$field] ?? $field;
            $nilaiLamaRaw = $this->nilai_lama[$field] ?? 'N/A';
            $nilaiBaruRaw = $this->nilai_baru[$field] ?? 'N/A';
            
            $nilaiLama = is_array($nilaiLamaRaw) ? implode(', ', $nilaiLamaRaw) : $nilaiLamaRaw;
            $nilaiBaru = is_array($nilaiBaruRaw) ? implode(', ', $nilaiBaruRaw) : $nilaiBaruRaw;
            
            $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
        }
        
        return implode('; ', $deskripsi);
    }
}
