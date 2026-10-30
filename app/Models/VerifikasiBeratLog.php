<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VerifikasiBeratLog extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_berat_logs';

    protected $fillable = [
        'uuid',
        'verifikasi_berat_id',
        'verifikasi_berat_uuid',
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

    // Relasi ke VerifikasiBerat
    public function verifikasiBerat()
    {
        return $this->belongsTo(VerifikasiBerat::class, 'verifikasi_berat_id');
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
            'tanggal' => 'Tanggal',
            'shift_id' => 'Shift',
            'id_plan' => 'Plan',
            'nama_produk' => 'Nama Produk',
            'kode_produksi' => 'Kode Produksi',
            'berat_standar' => 'Berat Standar',
            'berat_aktual' => 'Berat Aktual',
            'selisih' => 'Selisih',
            'persentase_selisih' => 'Persentase Selisih',
            'status_verifikasi' => 'Status Verifikasi',
            'catatan' => 'Catatan',
            'tahapan_produksi' => 'Tahapan Produksi',
            'jam_verifikasi' => 'Jam Verifikasi',
            'suhu_produk' => 'Suhu Produk',
            'kondisi_produk' => 'Kondisi Produk',
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
