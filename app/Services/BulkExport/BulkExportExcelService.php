<?php
namespace App\Services\BulkExport;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\DataShift;
use App\Models\JenisProduk;

abstract class BulkExportExcelService
{
    abstract protected function model(): string;
    abstract protected function relations(): array;
    abstract protected function exportClass(Collection $data, array $filterInfo);
    abstract protected function filenamePrefix(): string;

    protected function extraValidationRules(): array
    {
        return [];
    }

    protected function orderColumn(): array
    {
        return ['tanggal', 'asc']; // [column, direction] — override per module
    }

    protected function baseQuery(): Builder
    {
        return $this->model()::with($this->relations());
    }

    protected function applyRoleScope(Builder $query, $user): Builder
    {
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        return $query;
    }

    protected function applyAuditScope(Builder $query, Request $request): Builder
    {
        if ($request->boolean('is_audit')) {
            $modelClass = $this->model();
            $auditedSourceUuids = $modelClass::where('is_audit', true)
                ->whereNotNull('source_uuid')
                ->pluck('source_uuid')
                ->toArray();

            return $query->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            });
        }

        return $query->operasional();
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

        $query->when($request->filled('shift_id'), fn ($q) => $q->where('id_shift', $request->shift_id))
              ->when($request->filled('id_produk'), fn ($q) => $q->where('id_produk', $request->id_produk))
              ->when($request->filled('line'), fn ($q) => $q->where('line', $request->line));

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
            'shift' => $request->shift_id ? (DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui') : 'Semua Shift',
            'produk' => $request->id_produk ? (JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui') : 'Semua Produk',
            'line' => $request->line ? 'Line ' . $request->line : 'Semua Line',
            'kode_form' => $request->kode_form,
        ];
    }

    public function handle(Request $request, callable $noDataResponder)
    {
        $request->validate(array_merge([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'line' => 'nullable|in:1,2,3,4,5,6,7,8',
            'kode_form' => 'required|string|max:255',
        ], $this->extraValidationRules()));

        [$col, $dir] = $this->orderColumn();

        $query = $this->applyFilters(
            $this->applyAuditScope(
                $this->applyRoleScope($this->baseQuery(), Auth::user()),
                $request
            ),
            $request
        )->orderBy($col, $dir);

        $data = $query->get();

        if ($data->isEmpty()) {
            return $noDataResponder('Tidak ada data yang sesuai dengan filter yang dipilih.');
        }

        if ($this->stampsKodeForm()) {
            $this->model()::whereIn('id', $data->pluck('id'))->update(['kode_form' => $request->kode_form]);
        }
        $data = $query->get(); // refresh

        $filename = $this->filenamePrefix() . '_' .
            ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') .
            '_' . time() . '.xlsx';

        return Excel::download($this->exportClass($data, $this->buildFilterInfo($request)), $filename);
    }

    protected function stampsKodeForm(): bool
    {
        return true;
    }
}