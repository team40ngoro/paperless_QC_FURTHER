<?php

namespace App\Models;

use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanChillroom extends Model implements ImportableModel
{
    use HasAudit, HasFactory;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'penerimaan_chillroom';

    protected $fillable = [
        'uuid',
        'nama_rm',
        'kode_produksi',
        'berat',
        'tanggal',
        'suhu',
        'shift_id',
        'jam_kedatangan',
        'sensori',
        'kemasan',
        'keterangan',
        'standar_berat',
        'jumlah_rm',
        'status_rm',
        'nilai_jumlah_rm',
        'catatan_rm',
        'id_plan',
        'user_id',
        'kode_form',
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
        'jam_kedatangan' => 'datetime:H:i',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'is_audit' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    // Relasi ke Plan
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    // Relasi ke Shift
    public function datashift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User yang melakukan approval
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

    public function qcVerifications()
    {
        return $this->hasMany(ChillroomQcVerification::class, 'penerimaan_chillroom_id')
            ->orderByDesc('verified_at');
    }

    public function latestQcVerification()
    {
        return $this->hasOne(ChillroomQcVerification::class, 'penerimaan_chillroom_id')
            ->latestOfMany('verified_at');
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
            'Nama RM' => 'nama_rm',
            'Kode Produksi' => 'kode_produksi',
            'Berat (kg)' => 'berat',
            'Tanggal' => 'tanggal',              // Optional - auto-filled if empty
            'Jam Kedatangan' => 'jam_kedatangan',      // Optional - auto-filled if empty
            'Suhu (°C)' => 'suhu',
            'Shift' => 'shift_id',
            'Sensori' => 'sensori',
            'Kemasan' => 'kemasan',
            'Standar Berat' => 'standar_berat',
            'Jumlah RM' => 'jumlah_rm',
            'Berat Atas (pcs)' => 'berat_atas',
            'Berat Std (pcs)' => 'berat_std',
            'Berat Bawah (pcs)' => 'berat_bawah',
            'Status RM' => 'status_rm',
            'Keterangan' => 'keterangan',
            'Catatan RM' => 'catatan_rm',
        ];
    }

    /**
     * Validation rules for import
     */
    public static function getImportRules(): array
    {
        return [
            'nama_rm' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:100',
            'berat' => 'required|numeric|min:0',
            'tanggal' => 'nullable|date_format:Y-m-d',  // Auto-filled if empty
            'jam_kedatangan' => 'nullable|date_format:H:i',    // Auto-filled if empty
            'suhu' => 'required|numeric',
            'shift_id' => 'required|integer|exists:data_shift,id', // Now expect integer ID
            'sensori' => 'required|in:✓ Oke,✘ Tidak Oke',
            'kemasan' => 'required|in:✓ Oke,✘ Tidak Oke',
            'standar_berat' => 'nullable|numeric|min:0',
            'jumlah_rm' => 'nullable|numeric|min:0',
            'berat_atas' => 'nullable|integer|min:0',
            'berat_std' => 'nullable|integer|min:0',
            'berat_bawah' => 'nullable|integer|min:0',
            'status_rm' => 'nullable|in:diterima,diretur',
            'keterangan' => 'nullable|string',
            'catatan_rm' => 'nullable|string',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
     */
    public static function getMasterDataFields(): array
    {
        // Get current user's plan for filtering
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'nama_rm' => [
                'label' => 'Nama RM',
                'source' => DataRm::class,
                'label_column' => 'nama_rm',
                'value_column' => 'nama_rm',
                'filter' => ['id_plan' => $idPlan],
                'list_source' => "='Master Data'!A2:A100",
            ],
            'shift_id' => [
                'label' => 'Shift',
                'source' => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter' => ['id_plan' => $idPlan],
                'list_source' => "='Master Data'!B2:B100",
            ],
            'tanggal' => [
                'label' => 'Tanggal',
                'options' => ['(Auto: Hari Ini)'],
                'list_source' => "='Master Data'!C2:C2",
            ],
            'jam_kedatangan' => [
                'label' => 'Jam Kedatangan',
                'options' => ['(Auto: Jam Sekarang)'],
                'list_source' => "='Master Data'!D2:D2",
            ],
            'sensori' => [
                'label' => 'Sensori',
                'options' => ['✓ Oke', '✘ Tidak Oke'],
                'list_source' => "='Master Data'!E2:E100",
            ],
            'kemasan' => [
                'label' => 'Kemasan',
                'options' => ['✓ Oke', '✘ Tidak Oke'],
                'list_source' => "='Master Data'!F2:F100",
            ],
            'status_rm' => [
                'label' => 'Status RM',
                'options' => ['diterima', 'diretur'],
                'list_source' => "='Master Data'!G2:G100",
            ],
        ];
    }

    /**
     * Transform row data before import
     */
    public static function transformImportRow(array $row): array
    {
        // Ensure kode_produksi is always a string (Excel sometimes reads it as number)
        if (isset($row['kode_produksi']) && is_numeric($row['kode_produksi'])) {
            $row['kode_produksi'] = (string) $row['kode_produksi'];
        }

        // Auto-fill tanggal jika kosong (gunakan hari ini)
        if (empty($row['tanggal']) || $row['tanggal'] === '' || $row['tanggal'] === null) {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        } else {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        // Auto-fill jam jika kosong (gunakan jam sekarang)
        if (empty($row['jam_kedatangan']) || $row['jam_kedatangan'] === '' || $row['jam_kedatangan'] === null) {
            $row['jam_kedatangan'] = Carbon::now('Asia/Jakarta')->format('H:i');
        } else {
            try {
                $row['jam_kedatangan'] = Carbon::parse($row['jam_kedatangan'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam_kedatangan'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        }

        // Transform shift from shift name (e.g., "1", "2", "3") to shift_id (integer)
        if (isset($row['shift_id'])) {
            $shiftValue = trim((string) $row['shift_id']);

            // Try to find shift by shift name first
            $shift = DataShift::where('shift', $shiftValue)->first();

            if ($shift) {
                $row['shift_id'] = (int) $shift->id;
            } else {
                // Try to treat it as an ID directly
                $shiftById = DataShift::find((int) $shiftValue);
                if ($shiftById) {
                    $row['shift_id'] = (int) $shiftValue;
                } else {
                    // If shift not found, throw error - validation will catch it
                    $row['shift_id'] = (int) $shiftValue;
                }
            }
        }

        // Nama RM: keep as-is and ensure it's a string
        if (isset($row['nama_rm'])) {
            $row['nama_rm'] = trim((string) $row['nama_rm']);
        }

        // Convert numeric strings to float
        if (isset($row['berat']) && ! is_null($row['berat'])) {
            $row['berat'] = (float) str_replace(',', '.', (string) $row['berat']);
        }

        if (isset($row['suhu']) && ! is_null($row['suhu'])) {
            $row['suhu'] = (float) str_replace(',', '.', (string) $row['suhu']);
        }

        if (isset($row['standar_berat']) && ! is_null($row['standar_berat'])) {
            $row['standar_berat'] = (float) str_replace(',', '.', (string) $row['standar_berat']);
        }

        if (isset($row['jumlah_rm']) && ! is_null($row['jumlah_rm'])) {
            $row['jumlah_rm'] = (int) $row['jumlah_rm'];
        }

        // Convert berat sampling fields to integer
        if (isset($row['berat_atas']) && ! is_null($row['berat_atas'])) {
            $row['berat_atas'] = (int) $row['berat_atas'];
        }

        if (isset($row['berat_std']) && ! is_null($row['berat_std'])) {
            $row['berat_std'] = (int) $row['berat_std'];
        }

        if (isset($row['berat_bawah']) && ! is_null($row['berat_bawah'])) {
            $row['berat_bawah'] = (int) $row['berat_bawah'];
        }

        // Ensure keterangan and catatan_rm are strings
        if (isset($row['keterangan'])) {
            $row['keterangan'] = trim((string) $row['keterangan']);
        }

        if (isset($row['catatan_rm'])) {
            $row['catatan_rm'] = trim((string) $row['catatan_rm']);
        }

        // Add UUID if not exists
        if (! isset($row['uuid'])) {
            $row['uuid'] = \Str::uuid();
        }

        return $row;
    }
}
