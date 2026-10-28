<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class GmpKaryawan extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'gmp_karyawan';

    protected $fillable = [
        'is_audit',
        'source_uuid',
        'uuid',
        'id_plan',
        'user_id',
        'id_area',
        'shift_id',
        'tanggal',
        'jam',
        'nama_karyawan',
        'temuan_ketidaksesuaian',
        'keterangan',
        'tindakan_koreksi',
        'verifikasi',
        'koreksi_lanjutan',
        'kode_form',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'approved_by_qc_at',
        'approved_by_produksi_at',
        'approved_by_spv_at',
        'approved_by_qc_user_id',
        'approved_by_produksi_user_id',
        'approved_by_spv_user_id',
        'entries',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'entries' => 'array',
        'approved_by_qc_at' => 'datetime',
        'approved_by_produksi_at' => 'datetime',
        'approved_by_spv_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan', 'id');
    }

    /**
     * Relationship to InputArea model
     */
    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id', 'id');
    }

    // Approval user relationships
    public function approvedByQcUser()
    {
        return $this->belongsTo(User::class, 'approved_by_qc_user_id');
    }

    public function approvedByProduksiUser()
    {
        return $this->belongsTo(User::class, 'approved_by_produksi_user_id');
    }

    public function approvedBySpvUser()
    {
        return $this->belongsTo(User::class, 'approved_by_spv_user_id');
    }
    // Scope untuk role-based access
    public function scopeForUser($query, $user)
    {
        if ($user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        return $query;
    }

    // Method untuk check apakah user bisa akses data ini
    public function canAccess($user)
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return $this->id_plan === $user->id_plan;
    }

    // Accessor untuk label temuan ketidaksesuaian
    public function getTemuanKetidaksesuaianLabelAttribute()
    {
        $labels = [
            'perlengkapan' => '1. Perlengkapan',
            'kuku' => '2. Kuku',
            'perhiasan' => '3. Perhiasan',
            'luka' => '4. Luka',
            'tidak_ada_temuan' => '5. Tidak ada temuan'
        ];

        return $labels[$this->temuan_ketidaksesuaian] ?? $this->temuan_ketidaksesuaian;
    }

    // Static method untuk mendapatkan options temuan
    public static function getTemuanOptions()
    {
        return [
            'perlengkapan' => '1. Perlengkapan',
            'kuku' => '2. Kuku',
            'perhiasan' => '3. Perhiasan',
            'luka' => '4. Luka',
            'tidak_ada_temuan' => '5. Tidak ada temuan'
        ];
    }

    // ===== ImportableModel Interface Implementation =====

    /**
     * Column mapping from Excel header to database field
     */
    public static function getImportMapping(): array
    {
        return [
            'Area' => 'id_area',
            'Shift' => 'shift_id',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Nama Karyawan' => 'nama_karyawan',
            'Temuan Ketidaksesuaian' => 'temuan_ketidaksesuaian',
            'Keterangan' => 'keterangan',
            'Tindakan Koreksi' => 'tindakan_koreksi',
            'Verifikasi' => 'verifikasi',
            'Koreksi Lanjutan' => 'koreksi_lanjutan',
        ];
    }

    /**
     * Validation rules for imported data
     */
    public static function getImportRules(): array
    {
        return [
            'id_area' => 'required|exists:input_areas,id',
            'shift_id' => 'required|exists:data_shifts,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'nama_karyawan' => 'required|string|max:255',
            'temuan_ketidaksesuaian' => 'required|in:perlengkapan,kuku,perhiasan,luka,tidak_ada_temuan',
            'keterangan' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
            'verifikasi' => 'nullable|string|max:1000',
            'koreksi_lanjutan' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
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
            'area' => [
                'label' => 'Area',
                'options' => $areas,
                'list_source' => "='Master Data'!A:A",
            ],
            'shift' => [
                'label' => 'Shift',
                'options' => $shifts,
                'list_source' => "='Master Data'!B:B",
            ],
            'temuan_ketidaksesuaian' => [
                'label' => 'Temuan Ketidaksesuaian',
                'options' => ['1. Perlengkapan', '2. Kuku', '3. Perhiasan', '4. Luka', '5. Tidak ada temuan'],
                'list_source' => "='Master Data'!C:C",
            ],
        ];
    }

    /**
     * Transform row data before validation
     */
    public static function transformImportRow(array $row): array
    {
        // Get area by name and convert to id_area
        if (isset($row['id_area'])) {
            $areaValue = trim((string) $row['id_area']);
            $area = InputArea::where('area', $areaValue)->first();
            if ($area) {
                $row['id_area'] = $area->id;
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

        // Convert temuan display value to database value
        if (isset($row['temuan_ketidaksesuaian'])) {
            $temuanValue = trim((string) $row['temuan_ketidaksesuaian']);
            $temuanMap = [
                '1. Perlengkapan' => 'perlengkapan',
                '2. Kuku' => 'kuku',
                '3. Perhiasan' => 'perhiasan',
                '4. Luka' => 'luka',
                '5. Tidak ada temuan' => 'tidak_ada_temuan'
            ];
            if (isset($temuanMap[$temuanValue])) {
                $row['temuan_ketidaksesuaian'] = $temuanMap[$temuanValue];
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
        $textFields = ['nama_karyawan', 'keterangan', 'tindakan_koreksi', 'verifikasi', 'koreksi_lanjutan'];
        foreach ($textFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = trim($row[$field]) ?: null;
            }
        }

        return $row;
    }
}
