<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class LabelStartUp extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected $fillable = [
        'uuid', 'id_plan', 'id_shift', 'user_id', 'tanggal',
        'kode_form', 'is_audit', 'source_uuid',
        'approved_by_qc', 'qc_approved_by', 'qc_approved_at',
        'approved_by_produksi', 'produksi_approved_by', 'produksi_approved_at',
        'approved_by_spv', 'spv_approved_by', 'spv_approved_at'
    ];

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $casts = [
        'is_audit' => 'boolean',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(LabelStartUpDetail::class);
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(\App\Models\DataShift::class, 'id_shift');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
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

    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        if (!$this->keepApprovalsOnAudit()) {
            $booleanFields = ['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'];
            $nullableFields = [
                'qc_approved_by', 'qc_approved_at',
                'produksi_approved_by', 'produksi_approved_at',
                'spv_approved_by', 'spv_approved_at',
            ];
            foreach ($booleanFields as $field) {
                if (array_key_exists($field, $clone->getAttributes())) {
                    $clone->{$field} = false;
                }
            }
            foreach ($nullableFields as $field) {
                if (array_key_exists($field, $clone->getAttributes())) {
                    $clone->{$field} = null;
                }
            }
        }

        $clone->save();

        // Clone detail records
        foreach ($this->details as $detail) {
            $dc = $detail->replicate();
            $dc->label_start_up_id = $clone->id;
            $dc->save();
        }

        return $clone;
    }

    /**
     * Get the import column mapping for Excel.
     * Key = header Excel, Value = field DB yang akan diisi.
     */
    public static function getImportMapping(): array
    {
        return [
            'Tanggal'           => 'tanggal',
            'Jam'               => 'jam',
            'Shift'             => 'id_shift',
            'Plant'             => 'id_plan',
            'Nama Produk'       => 'id_produk',
            'Berat (g)'         => 'berat',
            'Label Produk'      => 'label_produk',
            'Hasil Pengecekan'  => 'hasil_pengecekan',
            'Nama Operator'     => 'nama_operator',
            'Nama QC'           => 'nama_qc',
            'Tindakan Koreksi'  => 'tindakan_koreksi',
        ];
    }

    /**
     * Get validation rules for import.
     */
    public static function getImportRules(): array
    {
        return [
            'tanggal'           => 'required|date',
            'jam'               => 'nullable|string',
            'id_shift'          => 'required|integer',
            'id_plan'           => 'required|integer',
            'id_produk'         => 'required|integer',
            'berat'             => 'nullable|numeric|min:0',
            'label_produk'      => 'required|string',
            'hasil_pengecekan'  => 'required|string',
            'nama_operator'     => 'required|string',
            'nama_qc'           => 'required|string',
            'tindakan_koreksi'  => 'nullable|string',
        ];
    }

    /**
     * Get master data fields for Excel dropdowns.
     * Menggunakan format standar global (source class + list_source absolute reference).
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'id_shift' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => $idPlan ? ['id_plan' => $idPlan] : [],
                'list_source'  => "='Master Data'!\$A\$2:\$A\$100",
            ],
            'id_plan' => [
                'label'        => 'Plant',
                'source'       => Plan::class,
                'label_column' => 'nama_plan',
                'value_column' => 'nama_plan',
                'filter'       => $idPlan ? ['id' => $idPlan] : [],
                'list_source'  => "='Master Data'!\$B\$2:\$B\$100",
            ],
            'id_produk' => [
                'label'        => 'Nama Produk',
                'source'       => JenisProduk::class,
                'label_column' => 'nama_produk',
                'value_column' => 'nama_produk',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!\$C\$2:\$C\$100",
            ],
            'label_produk' => [
                'label'       => 'Label Produk',
                'options'     => ['PL', 'MC'],
                'list_source' => "='Master Data'!\$D\$2:\$D\$100",
            ],
            'berat' => [
                'label'       => 'Berat (g)',
                'options'     => ['-', 55, 100, 200, 225, 250, 300, 315, 360, 400, 450, 500, 700, 900, 1000, 1100, 2000],
                'list_source' => "='Master Data'!\$E\$2:\$E\$100",
            ],
            'hasil_pengecekan' => [
                'label'       => 'Hasil Pengecekan',
                'options'     => ['OK', 'Tidak OK'],
                'list_source' => "='Master Data'!\$F\$2:\$F\$100",
            ],
        ];
    }

    /**
     * Transform import row data before saving.
     * Resolve nama produk/shift/plan menjadi ID yang tepat.
     */
    public static function transformImportRow(array $row): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        // Parse tanggal
        if (!empty($row['tanggal'])) {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        } else {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
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

        // Resolve Shift name -> id_shift
        if (isset($row['id_shift'])) {
            $shiftVal = trim((string) $row['id_shift']);

            // Coba exact match dulu
            $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                              ->where('shift', $shiftVal)->first();

            // Jika gagal, ambil bagian pertama sebelum spasi (handle format "1 Medan")
            if (!$shift && str_contains($shiftVal, ' ')) {
                $shiftFirst = trim(explode(' ', $shiftVal)[0]);
                $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('shift', $shiftFirst)->first();
            }

            // Fallback: LIKE search
            if (!$shift) {
                $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('shift', 'like', "%{$shiftVal}%")->first();
            }

            $row['id_shift'] = $shift ? $shift->id : null;
        }

        // Resolve Plan (Plant) name -> id_plan
        if (isset($row['id_plan'])) {
            $planVal = trim((string) $row['id_plan']);
            $plan = Plan::where('nama_plan', $planVal)->first();
            $row['id_plan'] = $plan ? $plan->id : ($idPlan ?? null);
        } else {
            $row['id_plan'] = $idPlan ?? null;
        }

        // Resolve Produk name -> id_produk
        if (isset($row['id_produk'])) {
            $produkVal = trim((string) $row['id_produk']);
            $queryProduk = JenisProduk::when($idPlan, fn($q) => $q->where('id_plan', $idPlan));
            $produk = (clone $queryProduk)->where('nama_produk', $produkVal)->first()
                   ?? (clone $queryProduk)->where('nama_produk', 'like', "%{$produkVal}%")->first();
            $row['id_produk'] = $produk ? $produk->id : null;
        }

        // Cast berat ke numeric — nilai "-" atau non-numerik dijadikan null
        if (isset($row['berat'])) {
            $beratVal = (string) $row['berat'];
            if ($beratVal === '' || $beratVal === '-' || !is_numeric($beratVal)) {
                $row['berat'] = null;
            } else {
                $row['berat'] = (float) $beratVal;
            }
        }

        // Set user_id dari session auth
        $row['user_id'] = auth()->id();

        // Normalkan hasil_pengecekan: 'NG' -> 'Tidak OK' (safeguard template lama)
        if (isset($row['hasil_pengecekan']) && strtoupper(trim($row['hasil_pengecekan'])) === 'NG') {
            $row['hasil_pengecekan'] = 'Tidak OK';
        }

        return $row;
    }

    /**
     * Custom save hook untuk import Excel (dipanggil oleh ExcelImportService).
     * Menangani struktur master-detail:
     *  - Master (LabelStartUp): cari atau buat berdasarkan tanggal + shift + plan
     *  - Detail (LabelStartUpDetail): selalu buat baru per baris Excel
     */
    public static function saveImportRow(array $row): void
    {
        // Cari atau buat record header
        $master = self::firstOrCreate(
            [
                'tanggal'  => $row['tanggal'],
                'id_shift' => $row['id_shift'],
                'id_plan'  => $row['id_plan'],
                'is_audit' => false,
            ],
            [
                'uuid'    => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $row['user_id'] ?? auth()->id(),
            ]
        );

        // Simpan satu baris detail
        LabelStartUpDetail::create([
            'label_start_up_id'  => $master->id,
            'id_produk'          => $row['id_produk'] ?? null,
            'berat'              => $row['berat'] ?? null,
            'label_produk'       => $row['label_produk'] ?? null,
            'jam'                => $row['jam'] ?? now()->format('H:i'),
            'hasil_pengecekan'   => $row['hasil_pengecekan'] ?? 'OK',
            'nama_operator'      => $row['nama_operator'] ?? '',
            'ttd_operator'       => '',   // tidak ada TTD saat import Excel
            'nama_qc'            => $row['nama_qc'] ?? '',
            'ttd_qc'             => '',   // tidak ada TTD saat import Excel
            'foto_kode_produksi' => '',   // tidak ada foto saat import Excel
            'tindakan_koreksi'   => $row['tindakan_koreksi'] ?? null,
        ]);

    }
}
