<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\GmpKaryawan;
use App\Models\DataShift;
use App\Models\InputArea;
use App\Exports\GmpKaryawanExport;

class GmpKaryawanBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return GmpKaryawan::class;
    }

    protected function relations(): array
    {
        return ['plan', 'user', 'shift', 'area'];
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

    // Column here is `shift_id`, not the base's `id_shift`
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
        $areas = InputArea::all()->pluck('area', 'id');
        $temuanOptions = GmpKaryawan::getTemuanOptions();

        return new GmpKaryawanExport($data, $filterInfo, $areas, $temuanOptions);
    }

    protected function filenamePrefix(): string
    {
        return 'GMP_Karyawan';
    }
}