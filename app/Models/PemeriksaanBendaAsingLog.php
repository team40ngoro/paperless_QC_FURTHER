<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanBendaAsingLog extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_benda_asing_logs';

    protected $fillable = [
        'uuid',
        'pemeriksaan_benda_asing_id',
        'pemeriksaan_benda_asing_uuid',
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

    // Relasi ke PemeriksaanBendaAsing
    public function pemeriksaanBendaAsing()
    {
        return $this->belongsTo(PemeriksaanBendaAsing::class, 'pemeriksaan_benda_asing_id');
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
            'id_produk' => 'Produk',
            'tanggal' => 'Tanggal',
            // 'jam' => 'Jam',
            'jenis_kontaminasi' => 'Jenis Kontaminasi',
            'bukti' => 'Bukti Foto',
            'kode_produksi' => 'Kode Produksi',
            'ukuran_kontaminasi' => 'Ukuran Kontaminasi',
            'ditemukan' => 'Ditemukan',
            'analisa_masalah' => 'Analisa Masalah',
            'koreksi' => 'Koreksi',
            'tindak_korektif' => 'Tindak Korektif',
            'diketahui' => 'Diketahui'
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
            
            // Handle special formatting untuk file bukti
            if ($field === 'bukti') {
                $nilaiLama = $nilaiLama ? 'Ada file' : 'Tidak ada file';
                $nilaiBaru = $nilaiBaru ? 'Ada file' : 'Tidak ada file';
            }
            
            $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
        }
        
        return implode('; ', $deskripsi);
    }
}
