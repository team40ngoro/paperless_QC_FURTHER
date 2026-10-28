<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class ProdukYum extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'produk_yum';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_produk',
        'id_plan',
        'shift_id',
        'user_id',
        'id_data_bag',
        'kode_produksi',
        'kode_exp',
        'berat_pcs',
        'jumlah_pcs',
        'aktual_berat',
        'tanggal',
        'jam',
        'kode_form',
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
        'berat_pcs' => 'array',
        'jumlah_pcs' => 'array',
        'aktual_berat' => 'array',
        'tanggal' => 'datetime',
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
                $model->uuid = Str::uuid();
            }
        });
    }
    
    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dataBag()
    {
        return $this->belongsTo(DataBag::class, 'id_data_bag');
    }

    public function id_produk_bag()
    {
        return $this->belongsTo(DataBag::class, 'id_data_bag');
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
}
