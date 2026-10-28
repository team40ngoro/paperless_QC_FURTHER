<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemasakanNasiLog extends Model
{
    use HasFactory;

    protected $table = 'pemasakan_nasi_logs';

    protected $fillable = [
        'uuid',
        'pemasakan_nasi_id',
        'pemasakan_nasi_uuid',
        // 'user_id',
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

    // Relasi ke PemasakanNasi
    public function pemasakanNasi()
    {
        return $this->belongsTo(PemasakanNasi::class, 'pemasakan_nasi_id');
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
            'id_produk' => 'Produk',
            'kode_produksi' => 'Kode Produksi',
            'waktu_start' => 'Waktu Start',
            'waktu_stop' => 'Waktu Stop',
            'proses' => 'Proses',
            'waktu' => 'Waktu',
            'lama_proses' => 'Lama Proses',
            'temp_std_1' => 'Temp Std 1',
            'temp_std_2' => 'Temp Std 2',
            'temp_std_3' => 'Temp Std 3',
            'status_cooking' => 'Status Cooking',
            'sensori_kondisi' => 'Sensori Kondisi',
            'jenis_bahan' => 'Jenis Bahan',
            'jumlah' => 'Jumlah',
            'organo_warna' => 'Organo Warna',
            'organo_aroma' => 'Organo Aroma',
            'organo_rasa' => 'Organo Rasa',
            'organo_tekstur' => 'Organo Tekstur',
            'catatan' => 'Catatan',
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
             
             // Handle array values
             if (is_array($nilaiLama)) {
                 $nilaiLama = implode(', ', array_map('strval', $nilaiLama));
             }
             if (is_array($nilaiBaru)) {
                 $nilaiBaru = implode(', ', array_map('strval', $nilaiBaru));
             }
             
             $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
         }
         
         return implode('; ', $deskripsi);
     }
}
