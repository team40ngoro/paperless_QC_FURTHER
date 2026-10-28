<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\Seasoning;
use App\Models\DataShift;
use App\Exports\SeasoningExport;

class SeasoningBulkExportService extends BulkExportExcelService
{
    protected function model(): string
    {
        return Seasoning::class;
    }

    protected function relations(): array
    {
        return ['plan', 'shift', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function orderColumn(): array
    {
        return ['tanggal', 'asc'];
    }

    protected function extraValidationRules(): array
    {
        return [
            'shift_id' => 'nullable|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
        ];
    }

    protected function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_akhir)->endOfDay(),
            ]);
        } elseif ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->tanggal_awal)->startOfDay(),
                Carbon::parse($request->tanggal_akhir)->endOfDay(),
            ]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', $request->tanggal_mulai);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }

        return $query->when($request->filled('shift_id'), fn ($q) => $q->where('shift_id', $request->shift_id));
    }

    protected function buildFilterInfo(Request $request): array
    {
        $shift = $request->shift_id ? DataShift::find($request->shift_id) : null;
        $tglAwal = $request->tanggal_mulai ?? $request->tanggal_awal;
        $tglAkhir = $request->tanggal_akhir;
        $user = auth()->user();
        $plan = $user ? $user->getEffectivePlan() : null;
        $planNama = $plan ? $plan->nama_plan : 'SALATIGA';

        return [
            'tanggal' => $tglAwal && $tglAkhir
                ? Carbon::parse($tglAwal)->format('d/m/Y') . ' s/d ' . Carbon::parse($tglAkhir)->format('d/m/Y')
                : ($tglAwal ? Carbon::parse($tglAwal)->format('d/m/Y') : 'Semua Tanggal'),
            'shift' => $shift ? 'Shift ' . $shift->shift : 'Semua Shift',
            'kode_form' => $request->kode_form,
            'plan_nama' => strtoupper($planNama),
        ];
    }

    protected function exportClass(Collection $data, array $filterInfo)
    {
        return new SeasoningExport($data, $filterInfo);
    }

    protected function filenamePrefix(): string
    {
        return 'Seasoning';
    }

    protected function applyRoleScope(Builder $query, $user): Builder
    {
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        return $query;
    }
}
