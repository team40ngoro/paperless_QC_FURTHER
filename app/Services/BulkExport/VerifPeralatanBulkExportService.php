<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\VerifPeralatan;
use App\Models\DataShift;
use App\Exports\VerifPeralatanExport;

class VerifPeralatanBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return VerifPeralatan::class;
    }

    protected function relations(): array
    {
        return ['plan', 'user', 'shift', 'details.mesin.area', 'details.mesin.bagian', 'details.bagianDetails.bagian'];
    }

    protected function orderColumn(): array
    {
        return ['tanggal', 'desc']; // secondary orderBy('id','desc') from the PDF path not expressible here
    }

    protected function extraValidationRules(): array
    {
        return [
            'id_shift' => 'nullable|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
        ];
    }

    // Request field here is `id_shift`, not the base's `shift_id`
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

        return $query->when($request->filled('id_shift'), fn ($q) => $q->where('id_shift', $request->id_shift));
    }

    protected function buildFilterInfo(Request $request): array
    {
        return [
            'tanggal' => $request->filled('tanggal_awal')
                ? ($request->filled('tanggal_akhir')
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y')
                    : Carbon::parse($request->tanggal_awal)->format('d-m-Y'))
                : 'Semua Tanggal',
            'shift' => $request->id_shift ? (DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui') : 'Semua Shift',
            'kode_form' => $request->kode_form,
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new VerifPeralatanExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Verif_Peralatan';
    }
}