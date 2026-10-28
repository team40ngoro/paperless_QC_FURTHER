<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GmpKaryawanLog extends Model
{
    use HasFactory;

    protected $table = 'gmp_karyawan_logs';

    protected $fillable = [
        'uuid',
        'gmp_karyawan_id',
        'gmp_karyawan_uuid',
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

    // Relasi ke GmpKaryawan
    public function gmpKaryawan()
    {
        return $this->belongsTo(GmpKaryawan::class, 'gmp_karyawan_id');
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
            'nama_karyawan' => 'Nama Karyawan',
            'temuan_ketidaksesuaian' => 'Temuan Ketidaksesuaian',
            'keterangan' => 'Keterangan',
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
            
            // Handle enum values untuk temuan_ketidaksesuaian
            if ($field === 'temuan_ketidaksesuaian') {
                $temuanOptions = [
                    'sesuai' => 'Sesuai',
                    'perlengkapan' => '1. Perlengkapan',
                    'kuku' => '2. Kuku',
                    'perhiasan' => '3. Perhiasan',
                    'luka' => '4. Luka'
                ];
                $nilaiLama = $temuanOptions[$nilaiLama] ?? $nilaiLama;
                $nilaiBaru = $temuanOptions[$nilaiBaru] ?? $nilaiBaru;
            }
            
            $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
        }
        
        return implode('; ', $deskripsi);
    }
}
