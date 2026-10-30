<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesTwahingLog extends Model
{
    protected $table = 'proses_twahing_logs';
    
    protected $fillable = [
        'uuid',
        'proses_twahing_id',
        'proses_twahing_uuid',
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

    public function prosesTwahing()
    {
        return $this->belongsTo(ProsesTwahing::class, 'proses_twahing_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'jam' => 'Jam',
            'waktu_thawing_awal' => 'Waktu Thawing Awal',
            'waktu_thawing_akhir' => 'Waktu Thawing Akhir',
            'total_waktu_thawing_jam' => 'Total Waktu Thawing (Jam)',
            'kondisi_kemasan_rm' => 'Kondisi Kemasan RM',
            'approved_by_qc' => 'Disetujui QC',
            'approved_by_produksi' => 'Disetujui Produksi',
            'approved_by_spv' => 'Disetujui SPV'
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = self::getFieldNames();

        $namaField = [];
        if (is_array($this->field_yang_diubah)) {
            foreach ($this->field_yang_diubah as $field) {
                $namaField[] = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
            }
        }

        return implode(', ', $namaField);
    }

    public function getDeskripsiPerubahanAttribute()
    {
        $deskripsi = [];
        
        if (is_array($this->nilai_lama) && is_array($this->nilai_baru) && is_array($this->field_yang_diubah)) {
            foreach ($this->field_yang_diubah as $field) {
                $nilaiLama = $this->nilai_lama[$field] ?? 'Kosong';
                $nilaiBaru = $this->nilai_baru[$field] ?? 'Kosong';
                
                $namaField = $this->getNamaFieldSingle($field);
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }

        return implode('; ', $deskripsi);
    }

    public function getNamaFieldSingle($field)
    {
        $fieldNames = self::getFieldNames();

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
