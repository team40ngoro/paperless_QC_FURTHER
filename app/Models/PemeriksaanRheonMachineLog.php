<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanRheonMachineLog extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_rheon_machine_logs';

    protected $fillable = [
        'uuid',
        'pemeriksaan_rheon_machine_id',
        'pemeriksaan_rheon_machine_uuid',
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

    // Relasi ke PemeriksaanRheonMachine
    public function pemeriksaanRheonMachine()
    {
        return $this->belongsTo(PemeriksaanRheonMachine::class, 'pemeriksaan_rheon_machine_id');
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
            'batch' => 'Batch',
            'pukul' => 'Waktu',
            'id_produk' => 'Produk',
            'rata_rata_dough' => 'Rata-rata Dough',
            'rata_rata_filler' => 'Rata-rata Filler',
            'rata_rata_after_forming' => 'Rata-rata After Forming',
            'rata_rata_after_frying' => 'Rata-rata After Frying',
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
            $nilaiLama = $this->nilai_lama[$field] ?? 'N/A';
            $nilaiBaru = $this->nilai_baru[$field] ?? 'N/A';
            
            $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
        }
        
        return implode('; ', $deskripsi);
    }
}
