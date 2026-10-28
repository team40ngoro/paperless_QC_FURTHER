<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesMarinadeLog extends Model
{
    protected $table = 'proses_marinade_logs';
    
    protected $fillable = [
        'uuid',
        'proses_marinade_id',
        'proses_marinade_uuid',
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
     * Relasi ke tabel proses_marinade
     */
    public function prosesMarinadeModel()
    {
        return $this->belongsTo(ProsesMarinadeModel::class, 'proses_marinade_id');
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
            'id_shift' => 'Shift',
            'id_jenis_marinade' => 'Jenis Marinade',
            'kode_produksi' => 'Kode Produksi',
            'jumlah' => 'Jumlah',
            'tanggal' => 'Tanggal',
            'hasil_pencampuran' => 'Hasil Pencampuran',
            'id_plan' => 'Plan',
            'id_user' => 'User',
            'bahan_baku_tumbling_id' => 'Bahan Baku Tumbling ID',
            'bahan_baku_tumbling_uuid' => 'Bahan Baku Tumbling UUID'
        ];
    }
}
