<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesTumblingLog extends Model
{
    use HasFactory;

    protected $table = 'proses_tumbling_logs';

    protected $fillable = [
        'uuid',
        'proses_tumbling_id',
        'proses_tumbling_uuid',
        'user_id',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'ip_address',
        'user_agent',
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

    // Relationships
    public function prosesTumbling()
    {
        return $this->belongsTo(ProsesTumbling::class, 'proses_tumbling_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Field name mappings for user-friendly display
    public static function getFieldNames()
    {
        return [
            'id_plan' => 'ID Plan',
            'user_id' => 'User ID',
            'id_produk' => 'ID Produk',
            'id_tumbling' => 'ID Tumbling',
            'shift_id' => 'Shift ID',
            'aktual_drum_on' => 'Aktual Drum On',
            'aktual_drum_off' => 'Aktual Drum Off',
            'aktual_speed' => 'Aktual Speed',
            'aktual_total_waktu' => 'Aktual Total Waktu',
            'aktual_vakum' => 'Aktual Vakum',
            'waktu_mulai_tumbling' => 'Waktu Mulai Tumbling',
            'waktu_selesai_tumbling' => 'Waktu Selesai Tumbling',
            'suhu' => 'Suhu',
            'kondisi' => 'Kondisi',
            'waktu_mulai_aging' => 'Waktu Mulai Aging',
            'waktu_selesai_aging' => 'Waktu Selesai Aging',
            'kode_produksi' => 'Kode Produksi',
            'tanggal' => 'Tanggal',
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $fields = is_array($this->field_yang_diubah) ? $this->field_yang_diubah : [];
        
        return collect($fields)->map(function ($field) use ($fieldNames) {
            return $fieldNames[$field] ?? $field;
        })->implode(', ');
    }

    public function getDeskripsiPerubahanAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $fields = is_array($this->field_yang_diubah) ? $this->field_yang_diubah : [];
        $nilaiLama = is_array($this->nilai_lama) ? $this->nilai_lama : [];
        $nilaiBaru = is_array($this->nilai_baru) ? $this->nilai_baru : [];
        
        $descriptions = [];
        
        foreach ($fields as $field) {
            $namaField = $fieldNames[$field] ?? $field;
            $lama = $nilaiLama[$field] ?? 'Kosong';
            $baru = $nilaiBaru[$field] ?? 'Kosong';
            
            $descriptions[] = "{$namaField}: '{$lama}' → '{$baru}'";
        }
        
        return implode('; ', $descriptions);
    }

    /**
     * Helper method untuk mendapatkan nama field tunggal
     */
    public function getNamaFieldSingle($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
