<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\DataBarang;

use App\Models\Traits\HasAudit;

class BarangMudahPecah extends Model
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'barang_mudah_pecah';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'id_nama_barang',
        'jumlah',
        'kondisi',
        'kode_form',
        'temuan_ketidaksesuaian',
        'tindakan_koreksi',
        'verifikasi_hasil',
        'id_area',
        'id_sub_area',
        'is_manual',
        'nama_barang_manual',
        'nama_karyawan',
        'items_json',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'items_json' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function namaBarang()
    {
        return $this->belongsTo(DataBarang::class, 'id_nama_barang');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function subArea()
    {
        return $this->belongsTo(SubArea::class, 'id_sub_area');
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

    // Scopes for role-based access
    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        
        return $query->where('id_plan', $user->id_plan);
    }

    public function scopeForPlan($query, $planId)
    {
        return $query->where('id_plan', $planId);
    }

    // Helper methods for display
    public function getStatusBadgeClass()
    {
        return $this->kondisi === 'OK' ? 'badge-success' : 'badge-danger';
    }

    public function getStatusIcon()
    {
        return $this->kondisi === 'OK' ? 'fas fa-check' : 'fas fa-times';
    }

    public function getKondisiSymbol()
    {
        return $this->kondisi === 'OK' ? '✓' : '✗';
    }

    // Get route key name for UUID routing
    public function getRouteKeyName()
    {
        return 'uuid';
    }
    // 
}
