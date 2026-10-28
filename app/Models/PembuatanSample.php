<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class PembuatanSample extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected $table = 'pembuatan_sample';

    protected $fillable = [
        'uuid',
        'id_produk',
        'id_plan',
        'created_by',
        'id_shift',
        'kode_produksi',
        'tanggal',
        'jam',
        'tanggal_expired',
        'jumlah',
        'berat',
        'berat_sampling',
        'jenis_sample',
        'kode_form',
        'items_json',
        'is_audit',
        'source_uuid',
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
        'is_audit' => 'boolean',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'items_json' => 'array',
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
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

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    // ===== ImportableModel Interface Implementation =====

    public static function getImportMapping(): array
    {
        return [
            'Produk'             => 'id_produk',
            'Kode Produksi'     => 'kode_produksi',
            'Tanggal'           => 'tanggal',
            'Jam'               => 'jam',
            'Tanggal Expired'   => 'tanggal_expired',
            'Jumlah'            => 'jumlah',
            'Berat Sampel (g)'        => 'berat',
            'Gramase (g)' => 'berat_sampling',
            'Jenis Sample'      => 'jenis_sample',
            'Shift'             => 'id_shift',
        ];
    }

    public static function getImportRules(): array
    {
        return [
            'id_produk'         => 'required|integer|exists:jenis_produk,id',
            'kode_produksi'     => 'required|string|max:100',
            'tanggal'           => 'nullable',
            'jam'               => 'nullable',
            'tanggal_expired'   => 'required|date_format:Y-m-d',
            'jumlah'            => 'required|integer|min:1',
            'berat'             => 'required|numeric|min:0',
            'berat_sampling'    => 'required|numeric|min:0',
            'jenis_sample'      => 'required|string|max:255',
            'id_shift'          => 'required|integer|exists:data_shift,id',
        ];
    }

    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'id_produk' => [
                'label'        => 'Produk',
                'source'       => JenisProduk::class,
                'label_column' => 'nama_produk',
                'value_column' => 'nama_produk',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!\$A\$2:\$A\$100",
            ],
            'id_shift' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!\$B\$2:\$B\$100",
            ],
            'berat_sampling' => [
                'label'       => 'Gramase (g)',
                'options'     => ['-', '55', '100', '200', '225', '250', '300', '315', '360', '400', '450', '500', '700', '900', '1000', '1100', '2000'],
                'list_source' => "='Master Data'!\$C\$2:\$C\$100",
            ],
            'jenis_sample' => [
                'label'       => 'Jenis Sample',
                'options'     => ['Sample RND', 'Sample Lab', 'Sample Trial', 'Sample Retain'],
                'list_source' => "='Master Data'!\$D\$2:\$D\$100",
            ],
        ];
    }

    public static function transformImportRow(array $row): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        if (isset($row['kode_produksi']) && is_numeric($row['kode_produksi'])) {
            $row['kode_produksi'] = (string) $row['kode_produksi'];
        }

        if (empty($row['tanggal']) || $row['tanggal'] === '' || $row['tanggal'] === null) {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
            }
        }

        if (empty($row['jam']) || $row['jam'] === '' || $row['jam'] === null) {
            $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
        } else {
            try {
                $row['jam'] = Carbon::parse($row['jam'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        }

        if (isset($row['tanggal_expired']) && !empty($row['tanggal_expired'])) {
            try {
                $row['tanggal_expired'] = Carbon::parse($row['tanggal_expired'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Keep original string if parse fails
            }
        }

        // Map Produk name or ID to id_produk
        if (isset($row['id_produk'])) {
            $produkVal = trim((string) $row['id_produk']);
            $produk = JenisProduk::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                ->where('nama_produk', $produkVal)
                ->first();

            if (!$produk) {
                $produk = JenisProduk::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                    ->where('nama_produk', 'like', "%{$produkVal}%")
                    ->first();
            }

            if ($produk) {
                $row['id_produk'] = (int) $produk->id;
            } elseif (is_numeric($produkVal)) {
                $produkById = JenisProduk::find((int) $produkVal);
                if ($produkById) {
                    $row['id_produk'] = (int) $produkById->id;
                } else {
                    $row['id_produk'] = (int) $produkVal;
                }
            }
        }

        // Map Shift name or ID to id_shift
        if (isset($row['id_shift'])) {
            $shiftVal = trim((string) $row['id_shift']);
            $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                ->where(function($q) use ($shiftVal) {
                    $q->where('shift', $shiftVal)
                      ->orWhere('shift', 'Shift ' . $shiftVal)
                      ->orWhere('shift', 'like', "%{$shiftVal}%");
                })->first();

            if ($shift) {
                $row['id_shift'] = (int) $shift->id;
            } else {
                $shiftById = DataShift::find((int) $shiftVal);
                if ($shiftById) {
                    $row['id_shift'] = (int) $shiftById->id;
                } else {
                    $row['id_shift'] = (int) $shiftVal;
                }
            }
        }

        // Fallback jenis_sample jika kosong di Excel
        if (empty($row['jenis_sample'])) {
            $row['jenis_sample'] = 'Sample Retain';
        }

        // Auto Populate items_json for sample detail structure
        if (!isset($row['items_json']) || empty($row['items_json'])) {
            $row['items_json'] = [
                [
                    'id_produk'        => $row['id_produk'] ?? null,
                    'kode_produksi'    => $row['kode_produksi'] ?? '',
                    'tanggal_expired'  => $row['tanggal_expired'] ?? null,
                    'jumlah'           => $row['jumlah'] ?? 0,
                    'berat'            => $row['berat'] ?? 0,
                    'berat_sampling'   => $row['berat_sampling'] ?? null,
                    'jenis_sample'     => $row['jenis_sample'] ?? '',
                ]
            ];
        }

        if (!isset($row['uuid'])) {
            $row['uuid'] = (string) Str::uuid();
        }

        return $row;
    }
}
