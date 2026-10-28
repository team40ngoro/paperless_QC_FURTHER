<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanProsesProduksiLog extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_proses_produksi_logs';

    protected $fillable = [
        'uuid',
        'pemeriksaan_proses_produksi_id',
        'pemeriksaan_proses_produksi_uuid',
        'user_id',
        'user_name',
        'user_role',
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

    // Relasi ke PemeriksaanProsesProduksi
    public function pemeriksaanProsesProduksi()
    {
        return $this->belongsTo(PemeriksaanProsesProduksi::class, 'pemeriksaan_proses_produksi_id');
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
            'id_area' => 'Area',
            'shift_id' => 'Shift',
            'tanggal' => 'Tanggal',
            'ketidaksesuaian' => 'Ketidaksesuaian',
            'uraian_permasalahan' => 'Uraian Permasalahan',
            'analisa_penyebab' => 'Analisa Penyebab',
            'disposisi' => 'Disposisi',
            'tindakan_koreksi' => 'Tindakan Koreksi'
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
            $nilaiLama = $this->nilai_lama[$field] ?? 'N/A';
            $nilaiBaru = $this->nilai_baru[$field] ?? 'N/A';
            
            // Handle array values
            if (is_array($nilaiLama)) {
                $nilaiLama = implode(', ', array_map('strval', $nilaiLama));
            }
            if (is_array($nilaiBaru)) {
                $nilaiBaru = implode(', ', array_map('strval', $nilaiBaru));
            }
            
            // Handle special formatting untuk ketidaksesuaian
            if ($field === 'ketidaksesuaian') {
                $ketidaksesuaianOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
                $nilaiLama = $ketidaksesuaianOptions[$nilaiLama] ?? $nilaiLama;
                $nilaiBaru = $ketidaksesuaianOptions[$nilaiBaru] ?? $nilaiBaru;
            }
            
            // Handle special formatting untuk disposisi
            if ($field === 'disposisi') {
                $disposisiOptions = PemeriksaanProsesProduksi::getDisposisiOptions();
                $nilaiLama = $disposisiOptions[$nilaiLama] ?? $nilaiLama;
                $nilaiBaru = $disposisiOptions[$nilaiBaru] ?? $nilaiBaru;
            }
            
            // Handle datetime fields
            if ($field === 'tanggal') {
                if ($nilaiLama && $nilaiLama !== 'N/A') {
                    $nilaiLama = date('d/m/Y H:i:s', strtotime($nilaiLama));
                }
                if ($nilaiBaru && $nilaiBaru !== 'N/A') {
                    $nilaiBaru = date('d/m/Y H:i:s', strtotime($nilaiBaru));
                }
            }
            
            $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
        }
        
        return implode('; ', $deskripsi);
    }
}
