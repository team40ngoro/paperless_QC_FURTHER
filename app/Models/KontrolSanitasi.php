<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class KontrolSanitasi extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'kontrol_sanitasi';

    protected $fillable = [
        'is_audit',
        'source_uuid',
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'suhu_air',
        'kadar_klorin_food_basin',
        'kadar_klorin_hand_basin',
        'hasil_verifikasi',
        'entries',
        'kode_form',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'suhu_air' => 'string',
        'entries' => 'array',
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
    public function getFormattedSuhuAirAttribute()
    {
        return $this->suhu_air . '°C';
    }

    public function getStatusBadgeClass()
    {
        $hasil = strtolower($this->hasil_verifikasi);
        
        if (str_contains($hasil, 'sesuai') || str_contains($hasil, 'ok') || str_contains($hasil, 'baik')) {
            return 'badge-success';
        } elseif (str_contains($hasil, 'tidak') || str_contains($hasil, 'buruk') || str_contains($hasil, 'gagal')) {
            return 'badge-danger';
        } else {
            return 'badge-warning';
        }
    }

    public function getStatusIcon()
    {
        $hasil = strtolower($this->hasil_verifikasi);
        
        if (str_contains($hasil, 'sesuai') || str_contains($hasil, 'ok') || str_contains($hasil, 'baik')) {
            return 'fas fa-check';
        } elseif (str_contains($hasil, 'tidak') || str_contains($hasil, 'buruk') || str_contains($hasil, 'gagal')) {
            return 'fas fa-times';
        } else {
            return 'fas fa-exclamation';
        }
    }

    // Get route key name for UUID routing
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ===== ImportableModel Interface Implementation =====

    /**
     * Column mapping from Excel header to database field
     */
    public static function getImportMapping(): array
    {
        return [
            'Shift' => 'shift_id',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Suhu Air' => 'suhu_air',
            'Kadar Klorin Food Basin' => 'kadar_klorin_food_basin',
            'Kadar Klorin Hand Basin' => 'kadar_klorin_hand_basin',
            'Hasil Verifikasi' => 'hasil_verifikasi',
        ];
    }

    /**
     * Validation rules for imported data
     */
    public static function getImportRules(): array
    {
        return [
            'shift_id' => 'required|exists:data_shifts,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'suhu_air' => 'required|string|max:100',
            'kadar_klorin_food_basin' => 'required|string|max:100',
            'kadar_klorin_hand_basin' => 'required|string|max:100',
            'hasil_verifikasi' => 'required|string|max:255',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        // Get shifts filtered by plant
        $shiftsQuery = DataShift::orderBy('shift');
        if ($idPlan) {
            $shiftsQuery->where('id_plan', $idPlan);
        }
        $shifts = $shiftsQuery->pluck('shift')->values()->toArray();

        return [
            'shift' => [
                'label' => 'Shift',
                'options' => $shifts,
                'list_source' => "='Master Data'!A:A",
            ],
            'hasil_verifikasi' => [
                'label' => 'Hasil Verifikasi',
                'options' => ['Sesuai', 'Tidak Sesuai', 'OK', 'Tidak OK'],
                'list_source' => "='Master Data'!B:B",
            ],
        ];
    }

    /**
     * Transform row data before validation
     */
    public static function transformImportRow(array $row): array
    {
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

        // Add system fields
        $row['user_id'] = auth()->id();
        $row['id_plan'] = auth()->user()->id_plan;

        // Trim text fields
        $textFields = ['suhu_air', 'kadar_klorin_food_basin', 'kadar_klorin_hand_basin', 'hasil_verifikasi'];
        foreach ($textFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = trim($row[$field]);
            }
        }

        return $row;
    }
}
