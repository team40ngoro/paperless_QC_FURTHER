<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TimbanganLog extends Model
{
    use HasFactory;

    protected $table = 'timbangan_logs';

    protected $fillable = [
        'uuid',
        'timbangan_id',
        'timbangan_uuid',
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

    // Relasi ke Timbangan
    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class, 'timbangan_id');
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
            'shift_id' => 'Shift',
            'tanggal' => 'Tanggal',
            'jenis' => 'Jenis',
            'kode_timbangan' => 'Kode Timbangan',
            'hasil_pengecekan' => 'Hasil Pengecekan',
            'gram' => 'Kapasitas (Gram)'
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
            
            // Handle special formatting untuk hasil_pengecekan
            if ($field === 'hasil_pengecekan') {
                $hasilOptions = Timbangan::getHasilPengecekanOptions();
                $nilaiLama = $hasilOptions[$nilaiLama] ?? $nilaiLama;
                $nilaiBaru = $hasilOptions[$nilaiBaru] ?? $nilaiBaru;
            }
            
            // Handle special formatting untuk gram
            if ($field === 'gram') {
                $nilaiLama = $nilaiLama . ' Gram';
                $nilaiBaru = $nilaiBaru . ' Gram';
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
