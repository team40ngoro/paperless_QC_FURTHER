<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\Excel\Contracts\ImportableModel;

class InventarisQc extends Model implements ImportableModel
{
    use HasFactory;

    protected $table = 'inventaris_qc';

    protected $fillable = [
        'uuid',
        'id_plan',
        'id_shift',
        'user_id',
        'tanggal',
        'waktu_pemeriksaan',
        'catatan_umum',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(InventarisQcDetail::class, 'id_inventaris_qc');
    }

    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }

        return $query->where('id_plan', $user->id_plan);
    }

    /**
     * Return mapping untuk Excel import columns
     */
    public static function getImportMapping(): array
    {
        return [
            'Shift' => 'id_shift',
            'Tanggal' => 'tanggal',
            'Waktu Pemeriksaan' => 'waktu_pemeriksaan',
            'Catatan Umum' => 'catatan_umum',
        ];
    }

    /**
     * Return validation rules untuk Excel import
     */
    public static function getImportRules(): array
    {
        return [
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:Y-m-d',
            'waktu_pemeriksaan' => 'nullable|date_format:H:i',
            'catatan_umum' => 'nullable|string',
        ];
    }

    /**
     * Return master data fields untuk dropdown di template Excel
     */
    public static function getMasterDataFields(): array
    {
        return [
            'id_shift' => [
                'model' => DataShift::class,
                'display' => 'nama_shift',
                'value' => 'id',
                'filter' => fn($query) => $query->where('id_plan', auth()->user()->id_plan ?? 0),
            ],
        ];
    }

    /**
     * Transform import row data sebelum insert
     */
    public static function transformImportRow(array $row): array
    {
        return [
            'id_shift' => $row['id_shift'] ?? null,
            'tanggal' => $row['tanggal'] ?? null,
            'waktu_pemeriksaan' => $row['waktu_pemeriksaan'] ?? null,
            'catatan_umum' => $row['catatan_umum'] ?? null,
            'id_plan' => auth()->user()->id_plan,
            'user_id' => auth()->id(),
        ];
    }
}
