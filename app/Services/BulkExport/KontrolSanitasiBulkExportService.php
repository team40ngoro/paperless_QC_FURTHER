<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\KontrolSanitasi;
use App\Models\DataShift;
use App\Exports\KontrolSanitasiExport;

class KontrolSanitasiBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return KontrolSanitasi::class;
    }

    protected function relations(): array
    {
        return ['plan', 'user', 'shift'];
    }

    protected function orderColumn(): array
    {
        return ['created_at', 'desc'];
    }

    protected function extraValidationRules(): array
    {
        return [
            'shift_id' => 'nullable|integer',
            'kode_form' => 'required|string|max:50',
        ];
    }

    protected function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->tanggal_awal)->startOfDay(),
                Carbon::parse($request->tanggal_akhir)->endOfDay(),
            ]);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }

        return $query->when($request->filled('shift_id'), fn ($q) => $q->where('shift_id', $request->shift_id));
    }

    protected function buildFilterInfo(Request $request): array
    {
        return [
            'tanggal' => $request->filled('tanggal_awal')
                ? ($request->filled('tanggal_akhir')
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y')
                    : Carbon::parse($request->tanggal_awal)->format('d-m-Y'))
                : 'Semua Tanggal',
            'shift' => $request->shift_id ? (DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui') : 'Semua Shift',
            'kode_form' => $request->kode_form,
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new KontrolSanitasiExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Kontrol_Sanitasi';
    }
}