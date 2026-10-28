<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HasilPenggorenganLog extends Model
{
    use HasFactory;

    protected $table = 'hasil_penggorengan_logs';

    protected $fillable = [
        'uuid',
        'hasil_penggorengan_id',
        'hasil_penggorengan_uuid',
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
    public function hasilPenggorengan()
    {
        return $this->belongsTo(HasilPenggorengan::class, 'hasil_penggorengan_id');
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
            'user_id' => 'User',
            'id_shift' => 'Shift',
            'id_produk' => 'Produk',
            'id_std_suhu_pusat' => 'Standar Suhu Pusat',
            'aktual_suhu_pusat' => 'Aktual Suhu Pusat',
            'sensori' => 'Sensori',
            'sensori_kematangan' => 'Sensori - Kematangan',
            'sensori_kenampakan' => 'Sensori - Kenampakan',
            'sensori_warna' => 'Sensori - Warna',
            'sensori_rasa' => 'Sensori - Rasa',
            'sensori_bau' => 'Sensori - Bau',
            'sensori_tekstur' => 'Sensori - Tekstur',
            'tanggal' => 'Tanggal',
            'frayer_uuid' => 'Frayer UUID',
            'frayer2_uuid' => 'Frayer2 UUID',
            'breader_uuid' => 'Breader UUID',
            'battering_uuid' => 'Battering UUID',
            'predust_uuid' => 'Predust UUID',
            'penggorengan_uuid' => 'Penggorengan UUID'
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
                
                // Handle array values
                if (is_array($nilaiLama)) {
                    $nilaiLama = json_encode($nilaiLama);
                }
                if (is_array($nilaiBaru)) {
                    $nilaiBaru = json_encode($nilaiBaru);
                }
                
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }
        
        return implode(' | ', $deskripsi);
    }

    // Additional helper methods for logs view
    public function getUserNameAttribute()
    {
        return $this->user->name ?? 'System';
    }

    public function getUserRoleAttribute()
    {
        return $this->user->role ?? 'N/A';
    }
}
