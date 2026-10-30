<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\ProdukNonForming;
use App\Exports\ProdukNonFormingExport;

class ProdukNonFormingBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return ProdukNonForming::class;
    }

    protected function relations(): array
    {
        return ['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover', 'formulaNonForming'];
    }

    protected function orderColumn(): array
    {
        return ['tanggal', 'desc'];
    }

    protected function extraValidationRules(): array
    {
        return [
            'shift' => 'nullable|string',
            'produk' => 'nullable|string',
            'kode_form' => 'required|string',
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

        return $query
            ->when($request->filled('shift'), fn ($q) => $q->whereHas('shift', fn ($sq) => $sq->where('shift', $request->shift)))
            ->when($request->filled('produk'), fn ($q) => $q->whereHas('produk', fn ($pq) => $pq->where('nama_produk', $request->produk)));
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
            'produk' => $request->produk ?: 'Semua Produk',
            'kode_form' => $request->kode_form,
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new ProdukNonFormingExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Produk_Non_Forming';
    }
}