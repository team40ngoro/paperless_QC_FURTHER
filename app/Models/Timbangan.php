<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\Contracts\ImportableModel;

class Timbangan extends Model implements ImportableModel
{
    use HasFactory, \App\Models\Traits\HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'timbangan';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'kode_form',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'area',
        'jenis',
        'kode_timbangan',
        'hasil_pengecekan',
        'gram',
        'hasil_verifikasi_500',
        'hasil_verifikasi_1000',
        'hasil_verifikasi_10000',
        'entries',
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
        'is_audit' => 'boolean',
        'tanggal' => 'datetime',
        'entries' => 'array',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];

    /**
     * Boot function untuk auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Route key name untuk menggunakan UUID sebagai parameter
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship ke Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship ke DataShift
     */
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    /**
     * Scope untuk filter berdasarkan role user
     */
    public function scopeFilterByRole($query)
    {
        $user = Auth::user();
        
        if ($user && $user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        
        return $query;
    }

    /**
     * Cek apakah user dapat mengakses data ini
     */
    public function canAccess()
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->role === 'superadmin') {
            return true;
        }
        
        return $this->id_plan == $user->id_plan;
    }

    /**
     * Accessor untuk label hasil pengecekan dengan simbol
     */
    public function getHasilPengecekanLabelAttribute()
    {
        $labels = [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];

        return $labels[$this->hasil_pengecekan] ?? $this->hasil_pengecekan;
    }

    /**
     * Accessor untuk label gram
     */
    public function getGramLabelAttribute()
    {
        $options = self::getGramOptions();
        return $options[$this->gram] ?? $this->gram;
    }

    /**
     * Relationship untuk QC approver
     */
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    /**
     * Relationship untuk Produksi approver
     */
    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    /**
     * Relationship untuk SPV approver
     */
    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    /**
     * Static method untuk mendapatkan options hasil pengecekan
     */
    public static function getHasilPengecekanOptions()
    {
        return [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];
    }

    /**
     * Static method untuk mendapatkan options gram
     */
    public static function getGramOptions()
    {
        return [
            '500' => '500 Gram',
            '1000' => '1000 Gram',
            '10000' => '10000 Gram'
        ];
    }

    /**
     * Return mapping untuk Excel import columns
     */
    public static function getImportMapping(): array
    {
        return [
            'Area' => 'area',
            'Shift' => 'shift_id',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Nama Timbangan' => 'nama_timbangan',
            'Hasil Verifikasi 500' => 'hasil_verifikasi_500',
            'Hasil Verifikasi 1000' => 'hasil_verifikasi_1000',
            'Hasil Verifikasi 10000' => 'hasil_verifikasi_10000',
        ];
    }

    /**
     * Return validation rules untuk Excel import
     */
    public static function getImportRules(): array
    {
        return [
            'area' => 'nullable|string',
            'shift_id' => 'nullable|exists:data_shift,id',  // Make nullable instead of required
            'tanggal' => 'nullable|string',  // Accept any string format first
            'jam' => 'nullable|string',      // Accept any string format first  
            'nama_timbangan' => 'nullable|string',
            'hasil_verifikasi_500' => 'nullable|string',
            'hasil_verifikasi_1000' => 'nullable|string',
            'hasil_verifikasi_10000' => 'nullable|string',
        ];
    }

    /**
     * Return master data fields untuk dropdown di template Excel
     */
    public static function getMasterDataFields(): array
    {
        return [
            'shift_id' => [
                'source' => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'label' => 'Shift',
                'filter' => [
                    'id_plan' => auth()->user()->id_plan ?? 0,
                ],
            ],
            'area' => [
                'source' => \App\Models\InputArea::class,
                'label_column' => 'area',
                'value_column' => 'area',
                'label' => 'Area',
                'filter' => [
                    'id_plan' => auth()->user()->id_plan ?? 0,
                ],
            ],
            'nama_timbangan' => [
                'source' => DataTimbangan::class,
                'label_column' => 'nama_timbangan',
                'value_column' => 'nama_timbangan',
                'label' => 'Nama Timbangan',
                'filter' => [
                    'id_plan' => auth()->user()->id_plan ?? 0,
                ],
            ],
        ];
    }

    /**
     * Transform import row data sebelum insert
     */
    public static function transformImportRow(array $row): array
    {
        $namaTimbangan = isset($row['nama_timbangan'])
            ? trim((string) $row['nama_timbangan'])
            : '';
        $kodeTimbangan = null;

        if ($namaTimbangan !== '') {
            $kodeTimbanganOptions = DataTimbangan::where('id_plan', auth()->user()->id_plan)
                ->where('nama_timbangan', $namaTimbangan)
                ->pluck('kode_timbangan');

            if ($kodeTimbanganOptions->count() === 1) {
                $kodeTimbangan = $kodeTimbanganOptions->first();
            }
        }

        // Get shift ID from shift name
        if (isset($row['shift_id']) && is_string($row['shift_id']) && trim($row['shift_id']) !== '') {
            $shiftValue = trim((string) $row['shift_id']);
            $shift = DataShift::where('shift', $shiftValue)
                ->where('id_plan', auth()->user()->id_plan ?? 0)
                ->first();
            if ($shift) {
                $row['shift_id'] = $shift->id;
            } else {
                // Fallback: gunakan shift pertama yang ada jika tidak ditemukan
                $defaultShift = DataShift::where('id_plan', auth()->user()->id_plan ?? 0)->first();
                $row['shift_id'] = $defaultShift ? $defaultShift->id : 1;
            }
        } else {
            // Fallback: gunakan shift pertama jika tidak ada nilai
            $defaultShift = DataShift::where('id_plan', auth()->user()->id_plan ?? 0)->first();
            $row['shift_id'] = $defaultShift ? $defaultShift->id : 1;
        }

        // Parse tanggal - set default if empty
        if (isset($row['tanggal']) && trim((string) $row['tanggal']) !== '') {
            try {
                $tanggal = \Carbon\Carbon::parse($row['tanggal'])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $tanggal = now()->format('Y-m-d H:i:s'); // Default to current datetime
            }
        } else {
            $tanggal = now()->format('Y-m-d H:i:s'); // Default to current datetime
        }

        // Parse jam - set default if empty
        if (isset($row['jam']) && trim((string) $row['jam']) !== '') {
            try {
                $jam = \Carbon\Carbon::parse($row['jam'])->format('H:i');
            } catch (\Exception $e) {
                $jam = now()->format('H:i'); // Default to current time
            }
        } else {
            $jam = now()->format('H:i'); // Default to current time
        }

        // Ensure hasil_verifikasi fields are strings (not null, not numeric)
        if (isset($row['hasil_verifikasi_500'])) {
            $row['hasil_verifikasi_500'] = $row['hasil_verifikasi_500'] !== null && $row['hasil_verifikasi_500'] !== '' 
                ? (string) $row['hasil_verifikasi_500'] 
                : null;
        }

        if (isset($row['hasil_verifikasi_1000'])) {
            $row['hasil_verifikasi_1000'] = $row['hasil_verifikasi_1000'] !== null && $row['hasil_verifikasi_1000'] !== '' 
                ? (string) $row['hasil_verifikasi_1000'] 
                : null;
        }

        if (isset($row['hasil_verifikasi_10000'])) {
            $row['hasil_verifikasi_10000'] = $row['hasil_verifikasi_10000'] !== null && $row['hasil_verifikasi_10000'] !== '' 
                ? (string) $row['hasil_verifikasi_10000'] 
                : null;
        }

        return [
            'area' => $row['area'] ?? null,
            'shift_id' => $row['shift_id'],
            'tanggal' => $tanggal,  // Always has value
            'jam' => $jam,          // Always has value
            'jenis' => $namaTimbangan !== '' ? $namaTimbangan : null,
            'kode_timbangan' => $kodeTimbangan,
            'hasil_verifikasi_500' => $row['hasil_verifikasi_500'] ?? null,
            'hasil_verifikasi_1000' => $row['hasil_verifikasi_1000'] ?? null,
            'hasil_verifikasi_10000' => $row['hasil_verifikasi_10000'] ?? null,
            'id_plan' => auth()->user()->id_plan,
            'user_id' => auth()->id(),
        ];
    }
}
