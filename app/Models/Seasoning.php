<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;
use Carbon\Carbon;

class Seasoning extends Model implements ImportableModel
{
    use HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }
    // id tetap auto increment (default)
    protected $fillable = [
        'uuid', 'id_plan', 'created_by', 'nama_rm', 'kode_produksi', 'berat', 'sensori', 'kemasan', 'keterangan','shift_id', 'tanggal', 'jam', 'kode_form',
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
    /**
     * Use UUID for route model binding.
     */
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

    /**
     * ============================================================
     * IMPORT EXCEL INTERFACE IMPLEMENTATION
     * ============================================================
     */

    /**
     * Column mapping for Excel import
     */
    public static function getImportMapping(): array
    {
        return [
            'Nama Seasoning'    => 'nama_rm',
            'Kode Produksi'     => 'kode_produksi',
            'Berat (kg)'        => 'berat',
            'Tanggal'           => 'tanggal',              // Optional - auto-filled if empty
            'Jam'               => 'jam',                  // Optional - auto-filled if empty
            'Shift'             => 'shift_id',
            'Sensori'           => 'sensori',
            'Kemasan'           => 'kemasan',
            'Keterangan'        => 'keterangan',
        ];
    }

    /**
     * Validation rules for import
     */
    public static function getImportRules(): array
    {
        return [
            'nama_rm'           => 'required|string|max:255',
            'kode_produksi'     => 'required|string|max:100',
            'berat'             => 'required|numeric|min:0',
            'tanggal'           => 'nullable|date_format:Y-m-d',  // Auto-filled if empty
            'jam'               => 'nullable|date_format:H:i',    // Auto-filled if empty
            'shift_id'          => 'required|integer|exists:data_shift,id',
            'sensori'           => 'required|in:✔ Baik,✘ Tidak Baik',
            'kemasan'           => 'required|in:✔ Baik,✘ Tidak Baik',
            'keterangan'        => 'nullable|string',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'nama_rm' => [
                'label'        => 'Nama Seasoning',
                'source'       => DataSeasoning::class,
                'label_column' => 'nama_seasoning',
                'value_column' => 'nama_seasoning',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!A2:A100",
            ],
            'shift_id' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!B2:B100",
            ],
            'tanggal' => [
                'label'       => 'Tanggal',
                'options'     => ['(Auto: Hari Ini)'],
                'list_source' => "='Master Data'!C2:C2",
            ],
            'jam' => [
                'label'       => 'Jam',
                'options'     => ['(Auto: Jam Sekarang)'],
                'list_source' => "='Master Data'!D2:D2",
            ],
            'sensori' => [
                'label'       => 'Sensori',
                'options'     => ['✔ Baik', '✘ Tidak Baik'],
                'list_source' => "='Master Data'!E2:E100",
            ],
            'kemasan' => [
                'label'       => 'Kemasan',
                'options'     => ['✔ Baik', '✘ Tidak Baik'],
                'list_source' => "='Master Data'!F2:F100",
            ],
        ];
    }

    /**
     * Transform row data before import
     */
    public static function transformImportRow(array $row): array
    {
        // Ensure kode_produksi is always a string
        if (isset($row['kode_produksi']) && is_numeric($row['kode_produksi'])) {
            $row['kode_produksi'] = (string) $row['kode_produksi'];
        }

        // Auto-fill tanggal jika kosong
        if (empty($row['tanggal']) || $row['tanggal'] === '' || $row['tanggal'] === null) {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        } else {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        // Auto-fill jam jika kosong
        if (empty($row['jam']) || $row['jam'] === '' || $row['jam'] === null) {
            $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
        } else {
            try {
                $row['jam'] = Carbon::parse($row['jam'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        }

        // Transform shift name to ID
        if (isset($row['shift_id'])) {
            $shiftValue = trim((string) $row['shift_id']);
            $shift = DataShift::where('shift', $shiftValue)->first();
            
            if ($shift) {
                $row['shift_id'] = (int) $shift->id;
            } else {
                $row['shift_id'] = (int) $shiftValue;
            }
        }

        // Ensure nama_rm is string
        if (isset($row['nama_rm'])) {
            $row['nama_rm'] = trim((string) $row['nama_rm']);
        }

        // Convert berat to float
        if (isset($row['berat']) && !is_null($row['berat'])) {
            $row['berat'] = (float) str_replace(',', '.', (string) $row['berat']);
        }

        // Ensure keterangan is string
        if (isset($row['keterangan'])) {
            $row['keterangan'] = trim((string) $row['keterangan']);
        }

        // Add UUID if not exists
        if (!isset($row['uuid'])) {
            $row['uuid'] = \Str::uuid();
        }

        return $row;
    }
}
