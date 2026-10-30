<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\PemeriksaanProsesProduksi;
use App\Models\DataShift;
use App\Models\InputArea;
use App\Exports\PemeriksaanProsesProduksiExport;

class PemeriksaanProsesProduksiBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return PemeriksaanProsesProduksi::class;
    }

    protected function relations(): array
    {
        return ['plan', 'user', 'shift', 'area', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function orderColumn(): array
    {
        return ['created_at', 'desc'];
    }

    protected function extraValidationRules(): array
    {
        return [
            'shift_id' => 'nullable|integer',
            'id_area' => 'nullable|integer',
            'kode_form' => 'nullable|string|max:50',
        ];
    }

    // Columns here are `shift_id` / `id_area`, not the base's `id_shift` / `id_produk`
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
            ->when($request->filled('shift_id'), fn ($q) => $q->where('shift_id', $request->shift_id))
            ->when($request->filled('id_area'), fn ($q) => $q->where('id_area', $request->id_area));
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
            'area' => $request->id_area ? (InputArea::find($request->id_area)->area ?? 'Tidak Diketahui') : 'Semua Area',
            'kode_form' => $request->kode_form ?? 'QF 24/00',
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new PemeriksaanProsesProduksiExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Pemeriksaan_Proses_Produksi';
    }
}