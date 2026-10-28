<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class PemeriksaanBendaAsing extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected $table = 'pemeriksaan_benda_asing';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'rm',
        'jumlah_terdampak',
        'berat',
        'tanggal',
        'jam',
        'jenis_kontaminasi',
        'bukti',
        'kode_produksi',
        'ukuran_kontaminasi',
        'ditemukan',
        'analisa_masalah',
        'koreksi',
        'tindak_korektif',
        'diketahui',
        'kode_form',
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
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'jam' => 'datetime:H:i:s',
        // Approval fields casting
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id', 'id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk', 'id');
    }

    // Approval relationships
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by', 'id');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by', 'id');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by', 'id');
    }

    // Scope untuk role-based access
    public function scopeForUser($query, $user)
    {
        if ($user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        return $query;
    }

    // Accessor untuk URL bukti foto
    public function getBuktiUrlAttribute()
    {
        return $this->bukti ? asset('storage/' . $this->bukti) : null;
    }

    // Method untuk check apakah user bisa akses data ini
    public function canAccess($user)
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return $this->id_plan === $user->id_plan;
    }

    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    // ===== ImportableModel Interface Implementation =====

    /**
     * Column mapping from Excel header to database field
     */
    public static function getImportMapping(): array
    {
        return [
            'Tanggal'            => 'tanggal',
            'Jam'                => 'jam',
            'Shift'              => 'shift_id',
            'Produk'             => 'id_produk',
            'RM'                 => 'rm',
            'Jenis Kontaminasi'  => 'jenis_kontaminasi',
            'Ukuran Kontaminasi' => 'ukuran_kontaminasi',
            'Ditemukan'          => 'ditemukan',
            'Diketahui'          => 'diketahui',
            'Analisa Masalah'    => 'analisa_masalah',
            'Tindak Korektif'    => 'tindak_korektif',
            'Kode Produksi'      => 'kode_produksi',
        ];
    }

    /**
     * Validation rules for imported data
     */
    public static function getImportRules(): array
    {
        return [
            'tanggal'            => 'required|date',
            'jam'                => 'nullable|string',
            'shift_id'           => 'required|integer',
            'id_produk'          => 'required|integer',
            'rm'                 => 'nullable|string|max:255',
            'jenis_kontaminasi'  => 'required|string|max:255',
            'ukuran_kontaminasi' => 'required|string|max:255',
            'ditemukan'          => 'required|string|max:255',
            'diketahui'          => 'nullable|string|max:255',
            'analisa_masalah'    => 'nullable|string',
            'tindak_korektif'    => 'nullable|string',
            'kode_produksi'      => 'required|string|max:255',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'shift_id' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => $idPlan ? ['id_plan' => $idPlan] : [],
                'list_source'  => "='Master Data'!\$A\$2:\$A\$100",
            ],
            'id_produk' => [
                'label'        => 'Produk',
                'source'       => JenisProduk::class,
                'label_column' => 'nama_produk',
                'value_column' => 'nama_produk',
                'filter'       => $idPlan ? ['id_plan' => $idPlan] : [],
                'list_source'  => "='Master Data'!\$B\$2:\$B\$100",
            ],
        ];
    }

    /**
     * Transform row data before validation
     */
    public static function transformImportRow(array $row): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        // Parse tanggal — toleran format Y-m-d atau d-m-Y
        if (!empty($row['tanggal'])) {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        // Parse jam
        if (!empty($row['jam'])) {
            try {
                $row['jam'] = Carbon::parse($row['jam'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        } else {
            $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
        }

        // Resolve shift_id: nama shift -> id (exact, strip, LIKE)
        if (isset($row['shift_id'])) {
            $shiftVal = trim((string) $row['shift_id']);

            $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                              ->where('shift', $shiftVal)->first();

            if (!$shift && str_contains($shiftVal, ' ')) {
                $first = trim(explode(' ', $shiftVal)[0]);
                $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('shift', $first)->first();
            }
            if (!$shift) {
                $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('shift', 'like', "%{$shiftVal}%")->first();
            }

            $row['shift_id'] = $shift ? $shift->id : null;
        }

        // Resolve id_produk: nama produk -> id (exact, LIKE)
        if (isset($row['id_produk'])) {
            $produkVal = trim((string) $row['id_produk']);

            $produk = JenisProduk::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                 ->where('nama_produk', $produkVal)->first()
                    ?? JenisProduk::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('nama_produk', 'like', "%{$produkVal}%")->first();

            $row['id_produk'] = $produk ? $produk->id : null;
        }

        // System fields
        $row['id_plan'] = $idPlan;
        $row['user_id'] = auth()->id();

        // Trim string fields
        foreach (['rm','jenis_kontaminasi','ukuran_kontaminasi','ditemukan','diketahui','analisa_masalah','tindak_korektif','kode_produksi'] as $field) {
            if (isset($row[$field])) {
                $row[$field] = trim((string) $row[$field]) ?: null;
            }
        }

        return $row;
    }
}
