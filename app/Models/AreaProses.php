<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class AreaProses extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'area_proses';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'group_uuid',
        'user_id',
        'id_plan',
        'area_id',
        'shift_id',
        'tanggal',
        'jam',
        'kebersihan_ruangan',
        'kebersihan_karyawan',
        'pemeriksaan_suhu_ruang',
        'suhu_rm',
        'ketidaksesuaian',
        'tindakan_koreksi',
        'kondisi_barang',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
        'kode_form',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        // Approval casts
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
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
            if (empty($model->group_uuid)) {
                // default group to itself if not provided
                $model->group_uuid = $model->uuid;
            }
        });
    }

    /**
     * Relationship to User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to Plan model
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship to InputArea model
     */
    public function area()
    {
        return $this->belongsTo(InputArea::class, 'area_id');
    }

    /**
     * Relationship to DataShift model
     */
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    /**
     * Relationship to QC Approver
     */
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    /**
     * Relationship to Produksi Approver
     */
    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    /**
     * Relationship to SPV Approver
     */
    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    /**
     * Scope for filtering by plan based on user role
     */
    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        
        return $query->where('id_plan', $user->id_plan);
    }

    /**
     * Scope for filtering by plan
     */
    public function scopeForPlan($query, $planId)
    {
        return $query->where('id_plan', $planId);
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClass($field)
    {
        $value = $this->$field;
        
        // For enum fields (kebersihan_ruangan, kebersihan_karyawan, pemeriksaan_suhu_ruang)
        if (in_array($field, ['kebersihan_ruangan', 'kebersihan_karyawan', 'pemeriksaan_suhu_ruang'])) {
            return $value === 'OK' ? 'badge-success' : 'badge-danger';
        }
        
        // For text fields (ketidaksesuaian, tindakan_koreksi)
        if (in_array($field, ['ketidaksesuaian', 'tindakan_koreksi'])) {
            return !empty($value) ? 'badge-info' : 'badge-secondary';
        }
        
        return 'badge-secondary';
    }

    /**
     * Get status icon for display
     */
    public function getStatusIcon($field)
    {
        $value = $this->$field;
        
        // For enum fields
        if (in_array($field, ['kebersihan_ruangan', 'kebersihan_karyawan', 'pemeriksaan_suhu_ruang'])) {
            return $value === 'OK' ? 'fas fa-check' : 'fas fa-times';
        }
        
        // For text fields
        if (in_array($field, ['ketidaksesuaian', 'tindakan_koreksi'])) {
            return !empty($value) ? 'fas fa-file-text' : 'fas fa-minus';
        }
        
        return 'fas fa-question';
    }

    // Get route key name for UUID routing
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship to other records in the same group
     */
    public function groupRecords()
    {
        return $this->hasMany(AreaProses::class, 'group_uuid', 'group_uuid');
    }

    /**
     * Map import columns to database fields
     * Format: ['Excel Header' => 'database_column']
     */
    public static function getImportMapping(): array
    {
        return [
            'Area' => 'area_id',
            'Shift' => 'shift_id',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Kebersihan Ruangan' => 'kebersihan_ruangan',
            'Kebersihan Karyawan' => 'kebersihan_karyawan',
            'Pemeriksaan Suhu Ruang' => 'pemeriksaan_suhu_ruang',
            'Suhu RM' => 'suhu_rm',
            'Ketidaksesuaian' => 'ketidaksesuaian',
            'Tindakan Koreksi' => 'tindakan_koreksi',
            'Kondisi Barang' => 'kondisi_barang',
        ];
    }

    /**
     * Define validation rules for imported data
     */
    public static function getImportRules(): array
    {
        return [
            'area_id' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'kebersihan_ruangan' => 'required|in:OK,NOT OK',
            'kebersihan_karyawan' => 'required|in:OK,NOT OK',
            'pemeriksaan_suhu_ruang' => 'required|string',
            'suhu_rm' => 'nullable|string',
            'ketidaksesuaian' => 'nullable|string|max:500',
            'tindakan_koreksi' => 'nullable|string|max:500',
            'kondisi_barang' => 'nullable|in:OK,NOT OK',
        ];
    }

    /**
     * Define master data fields for dropdown population
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        // Get areas filtered by plant
        $areasQuery = InputArea::orderBy('area');
        if ($idPlan) {
            $areasQuery->where('id_plan', $idPlan);
        }
        $areas = $areasQuery->pluck('area')->values()->toArray();

        // Get shifts filtered by plant
        $shiftsQuery = DataShift::orderBy('shift');
        if ($idPlan) {
            $shiftsQuery->where('id_plan', $idPlan);
        }
        $shifts = $shiftsQuery->pluck('shift')->values()->toArray();

        return [
            'area_id' => [
                'label' => 'Area',
                'options' => $areas,
                'list_source' => "='Master Data'!\$A\$2:\$A\$100",
            ],
            'shift_id' => [
                'label' => 'Shift',
                'options' => $shifts,
                'list_source' => "='Master Data'!\$B\$2:\$B\$100",
            ],
            'kebersihan_ruangan' => [
                'label' => 'Kebersihan Ruangan',
                'options' => ['OK', 'NOT OK'],
                'list_source' => "='Master Data'!\$C\$2:\$C\$100",
            ],
            'kebersihan_karyawan' => [
                'label' => 'Kebersihan Karyawan',
                'options' => ['OK', 'NOT OK'],
                'list_source' => "='Master Data'!\$D\$2:\$D\$100",
            ],
            'kondisi_barang' => [
                'label' => 'Kondisi Barang',
                'options' => ['OK', 'NOT OK'],
                'list_source' => "='Master Data'!\$E\$2:\$E\$100",
            ],
        ];
    }

    /**
     * Transform and validate row data before saving
     */
    public static function transformImportRow(array $row): array
    {
        // Get area by name and convert to area_id
        if (isset($row['area_id'])) {
            $areaValue = trim((string) $row['area_id']);
            $area = InputArea::where('area', $areaValue)->first();
            if ($area) {
                $row['area_id'] = $area->id;
            }
        }

        // Get shift by name and convert to shift_id
        if (isset($row['shift_id'])) {
            $shiftValue = trim((string) $row['shift_id']);
            $shift = DataShift::where('shift', $shiftValue)->first();
            if ($shift) {
                $row['shift_id'] = $shift->id;
            }
        }

        // Parse tanggal format
        if (isset($row['tanggal']) && !empty($row['tanggal'])) {
            try {
                $row['tanggal'] = \Carbon\Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        // Parse jam format H:i
        if (isset($row['jam']) && !empty($row['jam'])) {
            try {
                $row['jam'] = \Carbon\Carbon::createFromFormat('H:i', trim((string) $row['jam']))->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
            }
        }

        // Handle suhu_rm - can be comma-separated for multiple values
        if (isset($row['suhu_rm']) && !empty($row['suhu_rm'])) {
            $suhuRmValue = trim((string) $row['suhu_rm']);
            // Split by comma and trim each value, then rejoin with ", " format
            $suhuRmItems = array_filter(array_map('trim', explode(',', $suhuRmValue)), fn($val) => $val !== '' && !is_null($val));
            $row['suhu_rm'] = !empty($suhuRmItems) ? implode(', ', $suhuRmItems) : null;
        } else {
            $row['suhu_rm'] = null;
        }

        // Add system fields
        $row['user_id'] = auth()->id();
        $row['id_plan'] = auth()->user()->id_plan;

        // Trim text fields
        $textFields = ['ketidaksesuaian', 'tindakan_koreksi', 'kondisi_barang'];
        foreach ($textFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = trim($row[$field]) ?: null;
            }
        }

        return $row;
    }
}
