<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChillroomLog extends Model
{
    use HasFactory;

    protected $table = 'chillroom_logs';

    protected $fillable = [
        'uuid',
        'penerimaan_chillroom_id',
        'penerimaan_chillroom_uuid',
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

    // Relasi ke PenerimaanChillroom
    public function penerimaanChillroom()
    {
        return $this->belongsTo(PenerimaanChillroom::class, 'penerimaan_chillroom_id');
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
            'nama_rm' => 'Nama Raw Material',
            'kode_produksi' => 'Kode Produksi',
            'berat' => 'Berat',
            'tanggal' => 'Tanggal',
            'suhu' => 'Suhu',
            'shift_id' => 'Shift',
            'sensori' => 'Sensori',
            'kemasan' => 'Kemasan',
            'keterangan' => 'Keterangan',
            'standar_berat' => 'Standar Berat',
            'jumlah_rm' => 'Jumlah RM',
            'status_rm' => 'Status RM',
            'nilai_jumlah_rm' => 'Nilai Jumlah RM',
            'catatan_rm' => 'Catatan RM',
            'id_plan' => 'Plan',
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
