<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembekuanIqfPenggorenganLog extends Model
{
    use HasFactory;

    protected $table = 'pembekuan_iqf_penggorengan_logs';

    protected $fillable = [
        'uuid',
        'pembekuan_iqf_penggorengan_id',
        'pembekuan_iqf_penggorengan_uuid',
        'user_id',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'field_yang_diubah' => 'array',
        'nilai_lama' => 'array',
        'nilai_baru' => 'array'
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

    // Relationships
    public function pembekuanIqfPenggorengan()
    {
        return $this->belongsTo(PembekuanIqfPenggorengan::class, 'pembekuan_iqf_penggorengan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods for field name mapping
    public function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'id_shift' => 'Shift',
            'user_id' => 'User',
            'tanggal' => 'Tanggal',
            'suhu_ruang_iqf' => 'Suhu Ruang IQF',
            'holding_time' => 'Holding Time',
            'hasil_penggorengan_uuid' => 'Hasil Penggorengan',
            'frayer_uuid' => 'Frayer',
            'frayer2_uuid' => 'Frayer 2',
            'breader_uuid' => 'Breader',
            'battering_uuid' => 'Battering',
            'predust_uuid' => 'Predust',
            'penggorengan_uuid' => 'Penggorengan'
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $namaField = [];
        
        if (is_array($this->field_yang_diubah)) {
            foreach ($this->field_yang_diubah as $field) {
                $namaField[] = $fieldNames[$field] ?? $field;
            }
        }
        
        return implode(', ', $namaField);
    }

    public function getNamaFieldSingle($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? $field;
    }

    public function getDeskripsiPerubahanAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $deskripsi = [];
        
        if (is_array($this->field_yang_diubah) && is_array($this->nilai_lama) && is_array($this->nilai_baru)) {
            foreach ($this->field_yang_diubah as $index => $field) {
                $namaField = $fieldNames[$field] ?? $field;
                $nilaiLama = $this->nilai_lama[$index] ?? '-';
                $nilaiBaru = $this->nilai_baru[$index] ?? '-';
                
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }
        
        return implode(' | ', $deskripsi);
    }

    // Get user name for display
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }
}
