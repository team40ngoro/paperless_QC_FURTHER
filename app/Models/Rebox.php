<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\JenisProduk;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class Rebox extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $fillable = [
        'uuid', 'id_plan', 'created_by', 'nama_produk', 'kode_produksi', 'best_before', 'isi_jumlah', 'labelisasi', 'shift_id', 'tanggal', 'jam', 'kode_form',
        // Audit fields
        'is_audit',
        'source_uuid',
        // Approval fields
        'approved_by_qc',
        'qc_approved_by',
        'qc_approved_at',
        'approved_by_produksi',
        'produksi_approved_by',
        'produksi_approved_at',
        'approved_by_spv',
        'spv_approved_by',
        'spv_approved_at',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
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

    public function shift()
    {
        return $this->belongsTo(\App\Models\DataShift::class, 'shift_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Relasi ke User yang melakukan approval
    public function qcApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'spv_approved_by');
    }

    // ===== ImportableModel Interface Implementation =====

    public static function getImportMapping(): array
    {
        return [
            'Nama Produk' => 'nama_produk',
            'Kode Produksi' => 'kode_produksi',
            'Best Before' => 'best_before',
            'Isi Jumlah' => 'isi_jumlah',
            'Labelisasi' => 'labelisasi',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Shift' => 'shift_id',
        ];
    }

    public static function getImportRules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:100',
            'best_before' => 'required|date_format:Y-m-d',
            'isi_jumlah' => 'required|string|max:255',
            'labelisasi' => 'required|string|max:255',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer|exists:data_shift,id',
        ];
    }

    public static function getMasterDataFields(): array
    {
        return [
            'shift_id' => [
                'source' => 'data_shift',
                'column' => 'shift',
                'filter' => ['id_plan' => auth()->user()->id_plan]
            ],
        ];
    }

    public static function transformImportRow(array $row): array
    {
        // Transform shift name ke shift_id
        if (isset($row['shift'])) {
            $shift = DataShift::where('shift', $row['shift'])
                ->where('id_plan', auth()->user()->id_plan)
                ->firstOrFail();
            $row['shift_id'] = $shift->id;
            unset($row['shift']);
        }

        // Auto-fill tanggal jika kosong
        if (empty($row['tanggal'])) {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        }

        // Auto-fill jam jika kosong
        if (empty($row['jam'])) {
            $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
        }

        // Set other fields
        $row['id_plan'] = auth()->user()->id_plan;
        $row['created_by'] = auth()->id();

        return $row;
    }
}
