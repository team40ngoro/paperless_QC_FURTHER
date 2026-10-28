<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\KetidaksesuaianPlastik;
use App\Models\DataShift;
use App\Exports\KetidaksesuaianPlastikExport;

class KetidaksesuaianPlastikBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return KetidaksesuaianPlastik::class;
    }

    protected function relations(): array
    {
        return ['plan', 'user', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function orderColumn(): array
    {
        return ['created_at', 'desc'];
    }

    protected function extraValidationRules(): array
    {
        return [
            'shift_id' => 'nullable|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
        ];
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
        return new KetidaksesuaianPlastikExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Ketidaksesuaian_Plastik';
    }
}