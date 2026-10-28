<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HasAuditController
{
    /**
     * Model class yang menggunakan HasAudit trait
     * @return string
     */
    abstract protected function auditModel(): string;

    /**
     * Route prefix untuk naming route (contoh: 'chillroom', 'seasoning')
     * @return string
     */
    abstract protected function auditRoutePrefix(): string;

    /**
     * View prefix untuk naming view (contoh: 'qc-sistem.chillroom', 'qc-sistem.seasoning')
     * @return string
     */
    abstract protected function auditViewPrefix(): string;

    /**
     * Relasi yang di-load untuk index audit
     * @return array
     */
    protected function auditRelations(): array
    {
        return [];
    }

    /**
     * Kolom yang bisa di-search
     * @return array
     */
    protected function auditSearchColumns(): array
    {
        return ['nama_rm', 'kode_produksi', 'keterangan'];
    }

    /**
     * Menerapkan query pencarian untuk audit.
     * Dapat dioverride oleh controller jika membutuhkan filter relasi (whereHas).
     */
    protected function applyAuditSearchQuery($query, $search)
    {
        $query->where(function ($q) use ($search) {
            foreach ($this->auditSearchColumns() as $column) {
                $q->orWhere($column, 'LIKE', '%' . $search . '%');
            }
        });
    }

    /**
     * Kolom tanggal untuk grouping (default: 'tanggal')
     * @return string
     */
    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    /**
     * Kolom jam untuk ordering (default: 'jam_kedatangan')
     * @return string
     */
    protected function auditTimeColumn(): string
    {
        return 'jam_kedatangan';
    }

    /**
     * Relasi shift (default: 'shift')
     * @return string
     */
    protected function auditShiftRelation(): string
    {
        return 'shift';
    }

    /**
     * Kolom shift untuk grouping (default: 'shift_id')
     * @return string
     */
    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    /**
     * Kolom plan untuk filter (default: 'id_plan')
     * @return string
     */
    protected function auditPlanColumn(): string
    {
        return 'id_plan';
    }

    protected function applyAuditFilters(Request $request, $query): void
    {
    }

    /**
     * Smart Copy & Edit Audit:
     * Jika belum ada versi audit -> copy ke audit lalu langsung buka form edit audit.
     * Jika sudah ada versi audit -> langsung buka form edit audit dari versi yang sudah ada.
     */
    public function copyToAudit($uuid)
    {
        $model = $this->auditModel();
        $item = $model::where('uuid', $uuid)->firstOrFail();

        // Check permission
        if (!Auth::user()->hasPermissionTo('edit-' . $this->auditRoutePrefix()) &&
            !Auth::user()->hasPermissionTo('create-' . $this->auditRoutePrefix())) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit data audit.');
        }

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route("{$this->auditRoutePrefix()}.edit", [$item->uuid, 'is_audit' => 1]);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route("{$this->auditRoutePrefix()}.edit", [$item->auditVersion->uuid, 'is_audit' => 1]);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route("{$this->auditRoutePrefix()}.edit", [$clone->uuid, 'is_audit' => 1])
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    /**
     * Index halaman audit:
     * Menampilkan data audit (is_audit = true) DIPADUKAN DENGAN data operasional (is_audit = false)
     * yang BELUM memiliki versi audit (Audit-First Smart Filter).
     */
    public function auditIndex(Request $request)
    {
        $model = $this->auditModel();
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        // Ambil daftar source_uuid yang sudah di-copy ke audit agar bebas collation error
        $auditedSourceUuids = $model::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = $model::with($this->auditRelations())
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where($this->auditPlanColumn(), $user->id_plan);
            });

        if ($search) {
            $this->applyAuditSearchQuery($query, $search);
        }

        $this->applyAuditFilters($request, $query);
        $dateColumn = $this->auditDateColumn();
        $timeColumn = $this->auditTimeColumn();
        $dateRange = $this->applyDateRangeFilter($request, $query, $dateColumn);

        $records = $query->orderBy($dateColumn, 'desc')
            ->orderBy($timeColumn, 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Group by tanggal + shift
        $groupedData = $records->getCollection()->groupBy(function ($item) {
            $dateStr = $item->{$this->auditDateColumn()}
                ? (is_string($item->{$this->auditDateColumn()})
                    ? \Carbon\Carbon::parse($item->{$this->auditDateColumn()})->format('Y-m-d')
                    : $item->{$this->auditDateColumn()}->format('Y-m-d'))
                : 'no_date';
            return $dateStr . '_' . ($item->{$this->auditShiftColumn()} ?? $item->{$this->auditShiftRelation()} ?? 'no_shift');
        });

        $records->setCollection($groupedData);

        return view("{$this->auditViewPrefix()}.index_audit", array_merge(
            compact('records', 'search', 'perPage'),
            $dateRange
        ));
    }

    /**
     * AJAX search untuk audit (untuk datatable)
     */
    public function auditSearchAjax(Request $request)
    {
        try {
            $model = $this->auditModel();
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            // Ambil daftar source_uuid yang sudah di-copy ke audit agar bebas collation error
            $auditedSourceUuids = $model::where('is_audit', true)
                ->whereNotNull('source_uuid')
                ->pluck('source_uuid')
                ->toArray();

            $query = $model::with($this->auditRelations())
                ->where(function ($q) use ($auditedSourceUuids) {
                    $q->where('is_audit', true)
                      ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                          $subQ->where('is_audit', false);
                          if (!empty($auditedSourceUuids)) {
                              $subQ->whereNotIn('uuid', $auditedSourceUuids);
                          }
                      });
                })
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where($this->auditPlanColumn(), $user->id_plan);
                });

            if ($search) {
                $this->applyAuditSearchQuery($query, $search);
            }

            $this->applyAuditFilters($request, $query);
            $dateColumn = $this->auditDateColumn();
            $timeColumn = $this->auditTimeColumn();
            $this->applyDateRangeFilter($request, $query, $dateColumn);

            $records = $query->orderBy($dateColumn, 'desc')
                ->orderBy($timeColumn, 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(function ($item) {
                $dateStr = $item->{$this->auditDateColumn()}
                    ? (is_string($item->{$this->auditDateColumn()})
                        ? \Carbon\Carbon::parse($item->{$this->auditDateColumn()})->format('Y-m-d')
                        : $item->{$this->auditDateColumn()}->format('Y-m-d'))
                    : 'no_date';
                return $dateStr . '_' . ($item->{$this->auditShiftColumn()} ?? $item->{$this->auditShiftRelation()} ?? 'no_shift');
            });
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view("{$this->auditViewPrefix()}._table", compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Filter yang dikirim tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk menerapkan Audit-First Smart Query pada PDF Export Query
     */
    protected function applyAuditPdfScope($query, Request $request)
    {
        if ($request->boolean('is_audit')) {
            $model = $this->auditModel();
            $auditedSourceUuids = $model::where('is_audit', true)
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

        return $query->where('is_audit', false);
    }
}