<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembuatanPredustLog extends Model
{
    use HasFactory;

    protected $table = 'pembuatan_predust_logs';

    protected $fillable = [
        'uuid',
        'pembuatan_predust_id',
        'pembuatan_predust_uuid',
        'user_id',
        'user_name',
        'user_role',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'aksi',
        'keterangan',
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
    public function pembuatanPredust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'pembuatan_predust_id');
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
            'penggorengan_uuid' => 'Penggorengan',
            'id_produk' => 'Produk',
            'id_jenis_predust' => 'Jenis Predust',
            'tanggal' => 'Tanggal',
            'kondisi_predust' => 'Kondisi Predust',
            'hasil_pencetakan' => 'Hasil Pencetakan',
            'kode_produksi' => 'Kode Produksi'
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

    public function getNamaFieldSingle($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? $field;
    }
}
