<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InputMetalDetectorLog extends Model
{
    protected $table = 'input_metal_detector_logs';
    
    protected $fillable = [
        'uuid',
        'input_metal_detector_id',
        'input_metal_detector_uuid',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Relasi ke tabel input_metal_detector
     */
    public function inputMetalDetector()
    {
        return $this->belongsTo(InputMetalDetector::class, 'input_metal_detector_id');
    }

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Method untuk mendapatkan nama field yang user-friendly
     */
    public function getNamaFieldAttribute()
    {
        $fieldNames = $this->getFieldNames();

        $namaField = [];
        foreach ($this->field_yang_diubah as $field) {
            $namaField[] = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
        }

        return implode(', ', $namaField);
    }

    /**
     * Method untuk mendapatkan deskripsi perubahan yang mudah dibaca
     */
    public function getDeskripsiPerubahanAttribute()
    {
        $deskripsi = [];
        
        if ($this->nilai_lama && $this->nilai_baru) {
            foreach ($this->field_yang_diubah as $field) {
                $nilaiLama = $this->nilai_lama[$field] ?? 'Kosong';
                $nilaiBaru = $this->nilai_baru[$field] ?? 'Kosong';
                
                $namaField = $this->getNamaFieldSingle($field);
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }

        return implode('; ', $deskripsi);
    }

    /**
     * Helper method untuk mendapatkan nama field tunggal
     */
    public function getNamaFieldSingle($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Method untuk mendapatkan mapping field names
     */
    private function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'user_id' => 'User',
            'id_shift' => 'Shift',
            'id_produk' => 'Produk',
            'tanggal' => 'Tanggal',
            'berat_produk' => 'Berat Produk',
            'kode_produksi' => 'Kode Produksi',
            'fe_depan_aktual' => 'FE Depan Aktual',
            'fe_tengah_aktual' => 'FE Tengah Aktual',
            'fe_belakang_aktual' => 'FE Belakang Aktual',
            'non_fe_depan_aktual' => 'Non FE Depan Aktual',
            'non_fe_tengah_aktual' => 'Non FE Tengah Aktual',
            'non_fe_belakang_aktual' => 'Non FE Belakang Aktual',
            'sus_depan_aktual' => 'SUS Depan Aktual',
            'sus_tengah_aktual' => 'SUS Tengah Aktual',
            'sus_belakang_aktual' => 'SUS Belakang Aktual',
            'keterangan' => 'Keterangan'
        ];
    }
}
