<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembuatanEmulsiLog extends Model
{
    protected $table = 'pembuatan_emulsi_logs';
    
    protected $fillable = [
        'uuid',
        'pembuatan_emulsi_id',
        'pembuatan_emulsi_uuid',
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
     * Relasi ke tabel pembuatan_emulsi
     */
    public function pembuatanEmulsi()
    {
        return $this->belongsTo(PembuatanEmulsi::class, 'pembuatan_emulsi_id');
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
    public static function getFieldNames()
    {
        return [
            'kode_produksi_emulsi' => 'Kode Produksi Emulsi',
            'nomor_emulsi_id' => 'Nomor Emulsi',
            'nama_emulsi_id' => 'Nama Emulsi',
            'hasil_emulsi' => 'Hasil Emulsi',
            'shift_id' => 'Shift',
            'tanggal' => 'Tanggal',
            'id_produk' => 'Produk',
            'id_plan' => 'Plan',
            'suhu' => 'Suhu',
            'kode_form' => 'Kode Form',
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai'
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = self::getFieldNames();

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
        $fieldNames = self::getFieldNames();

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
