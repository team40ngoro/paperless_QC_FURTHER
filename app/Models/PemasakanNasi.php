<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Traits\HasAudit;

class PemasakanNasi extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'pemasakan_nasi';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'kode_form',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'tanggal',
        'jam',
        'kode_produksi',
        'waktu_start',
        'waktu_stop',
        'proses',
        'waktu',
        'jenis_bahan',
        'jumlah',
        'sensori_kondisi',
        'status_cooking',
        'lama_proses',
        'temp_std_1',
        'temp_std_2',
        'temp_std_3',
        'organo_warna',
        'organo_aroma',
        'organo_rasa',
        'organo_tekstur',
        'catatan',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at'
    ];

    protected $casts = [
        'jenis_bahan' => 'array',
        'jumlah' => 'array',
        'tanggal' => 'datetime',
        'status_cooking' => 'boolean',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime'
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    // Approval relationships
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    // Helper methods
    public function getStatusCookingTextAttribute()
    {
        return $this->status_cooking ? 'Aktif' : 'Tidak Aktif';
    }

    public function getJenisBahanArrayAttribute()
    {
        return is_array($this->jenis_bahan) ? $this->jenis_bahan : [];
    }

    public function getJumlahArrayAttribute()
    {
        return is_array($this->jumlah) ? $this->jumlah : [];
    }

    public static function getOrganoIcon($value)
    {
        return $value === 'OK' 
            ? '<i class="fas fa-check text-success"></i>' 
            : '<i class="fas fa-times text-danger"></i>';
    }

    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }
}
