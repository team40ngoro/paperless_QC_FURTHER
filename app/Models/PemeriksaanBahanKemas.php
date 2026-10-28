<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class PemeriksaanBahanKemas extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'pemeriksaan_bahan_kemas';

    protected $fillable = [
        'uuid',
        // Audit fields
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'nama_kemasan',
        'kode_produksi',
        'kondisi_bahan_kemasan',
        'keterangan',
        'kode_form',
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
        'tanggal' => 'datetime',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ===== ImportableModel Interface Implementation =====

    public static function getImportMapping(): array
    {
        return [
            'Nama Kemasan' => 'nama_kemasan',
            'Kode Produksi' => 'kode_produksi',
            'Kondisi Bahan Kemasan' => 'kondisi_bahan_kemasan',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Shift' => 'shift_id',
            'Keterangan' => 'keterangan',
        ];
    }

    public static function getImportRules(): array
    {
        return [
            'nama_kemasan' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:100',
            'kondisi_bahan_kemasan' => 'required|string|max:255',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer|exists:data_shift,id',
            'keterangan' => 'nullable|string',
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
        $row['user_id'] = auth()->id();

        return $row;
    }
}
