<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\AreaProses;
use App\Exports\AreaProsesExport;

class AreaProsesBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return AreaProses::class;
    }

    protected function relations(): array
    {
        return ['area', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function orderColumn(): array
    {
        return ['tanggal', 'desc'];
    }

    protected function extraValidationRules(): array
    {
        return [
            'shift' => 'nullable|string',
            'area' => 'nullable|string',
            'kode_form' => 'required|string|max:50',
        ];
    }

    // Filters here are by shift/area *name* via whereHas, not id — full override
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

        return $query
            ->when($request->filled('shift'), fn ($q) => $q->whereHas('shift', fn ($sq) => $sq->where('shift', $request->shift)))
            ->when($request->filled('area'), fn ($q) => $q->whereHas('area', fn ($aq) => $aq->where('area', $request->area)));
    }

    protected function buildFilterInfo(Request $request): array
    {
        return [
            'tanggal' => $request->filled('tanggal_awal')
                ? ($request->filled('tanggal_akhir')
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y')
                    : Carbon::parse($request->tanggal_awal)->format('d-m-Y'))
                : 'Semua Tanggal',
            'shift' => $request->shift ?: 'Semua Shift',
            'area' => $request->area ?: 'Semua Area',
            'kode_form' => $request->kode_form,
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new AreaProsesExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Area_Proses';
    }
}