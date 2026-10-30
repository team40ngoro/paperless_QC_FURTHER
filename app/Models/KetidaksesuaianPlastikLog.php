<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KetidaksesuaianPlastikLog extends Model
{
    use HasFactory;

    protected $table = 'ketidaksesuaian_plastik_logs';

    protected $fillable = [
        'uuid',
        'ketidaksesuaian_plastik_id',
        'ketidaksesuaian_plastik_uuid',
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

    // Relasi ke KetidaksesuaianPlastik
    public function ketidaksesuaianPlastik()
    {
        return $this->belongsTo(KetidaksesuaianPlastik::class, 'ketidaksesuaian_plastik_id');
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
            'nama_plastik' => 'Nama Plastik',
            'alasan_hold' => 'Alasan Hold',
            'hold_data' => 'Hold Data',
            'dokumentasi_tagging' => 'Dokumentasi Tagging',
            'dokumentasi_penyimpangan_plastik' => 'Dokumentasi Penyimpangan Plastik',
            'kode_form' => 'Kode Form',
            'approved_by_qc' => 'Approved by QC',
            'approved_by_produksi' => 'Approved by Produksi',
            'approved_by_spv' => 'Approved by SPV',
            'qc_approved_by' => 'QC Approved By',
            'produksi_approved_by' => 'Produksi Approved By',
            'spv_approved_by' => 'SPV Approved By',
            'qc_approved_at' => 'QC Approved At',
            'produksi_approved_at' => 'Produksi Approved At',
            'spv_approved_at' => 'SPV Approved At'
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
            
            // Handle boolean values
            if (is_bool($nilaiLama)) {
                $nilaiLama = $nilaiLama ? 'Ya' : 'Tidak';
            }
            if (is_bool($nilaiBaru)) {
                $nilaiBaru = $nilaiBaru ? 'Ya' : 'Tidak';
            }
            
            $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
        }
        
        return implode('; ', $deskripsi);
    }
}
