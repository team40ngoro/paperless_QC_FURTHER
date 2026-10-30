<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\VerifCip;
use App\Exports\VerifCipExport;

class VerifCipBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return VerifCip::class;
    }

    protected function relations(): array
    {
        return ['plan', 'user'];
    }

    protected function orderColumn(): array
    {
        return ['tanggal', 'desc']; // secondary orderBy('id','desc') from the PDF path not expressible here
    }

    protected function extraValidationRules(): array
    {
        return [
            'kode_form' => 'required|string|max:50',
        ];
    }

    // No shift/area/produk filters on this module — date range only
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

        return $query;
    }

    protected function buildFilterInfo(Request $request): array
    {
        return [
            'tanggal' => $request->filled('tanggal_awal')
                ? ($request->filled('tanggal_akhir')
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y')
                    : Carbon::parse($request->tanggal_awal)->format('d-m-Y'))
                : 'Semua Tanggal',
            'kode_form' => $request->kode_form,
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new VerifCipExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Verif_CIP';
    }

    protected function stampsKodeForm(): bool
    {
        return false;
    }
}