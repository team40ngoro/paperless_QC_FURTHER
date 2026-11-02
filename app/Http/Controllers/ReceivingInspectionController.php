<?php

namespace App\Http\Controllers;

use App\Exceptions\ReceivingInspectionException;
use App\Exports\ReceivingInspectionExport;
use App\Models\DataShift;
use App\Models\ReceivingInspection;
use App\Services\ReceivingInspection\ReceivingInspectionAccess;
use App\Services\ReceivingInspection\ReceivingInspectionRegistry;
use App\Services\ReceivingInspection\ReceivingInspectionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ReceivingInspectionController extends Controller
{
    public function __construct(
        private ReceivingInspectionService $service,
        private ReceivingInspectionRegistry $registry,
        private ReceivingInspectionAccess $access
    ) {
    }

    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->get('per_page', 25)));
        $filters = $request->only(['area', 'date_from', 'date_to', 'prod_status', 'qc_status', 'has_qc_finding', 'search']);
        $records = $this->service->query($filters)->paginate($perPage)->appends($request->query());

        return view('receiving-inspections.index', [
            'records' => $records,
            'areas' => $this->registry->areas(),
            'registry' => $this->registry,
            'access' => $this->access,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $this->assertAllowed($this->access->canCreate(auth()->user()), 'Only a Production Input Checker or Admin can create an inspection record.');

        return view('receiving-inspections.create', [
            'areas' => $this->registry->areas(),
            'registry' => $this->registry,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $record = $this->service->create(auth()->user(), $request->only(['area', 'inspection_date']));
        } catch (ReceivingInspectionException $e) {
            abort($e->status, $e->getMessage());
        }

        return redirect()->route('receiving-inspections.show', $record)->with('success', 'Inspection record created.');
    }

    public function show(ReceivingInspection $inspection)
    {
        $inspection->load(['createdBy', 'prodFilledBy', 'qcFilledBy']);
        $permissions = $this->access->permissions(auth()->user(), $inspection);
        $canViewProduction = $permissions['canViewProduction'] ?? false;
        $canViewQc = $permissions['canViewQc'] ?? false;
        $auditLogs = $this->access->canViewAudit(auth()->user(), $inspection)
            ? $this->service->auditLogs($inspection)
            : collect();

        return view('receiving-inspections.show', [
            'record' => $inspection,
            'area' => $inspection->area,
            'production' => ($canViewProduction || $canViewQc) ? ($inspection->production_data ?? []) : [],
            'qc' => $canViewQc ? ($inspection->qc_data ?? []) : [],
            'defects' => $canViewQc ? $inspection->defects()->get()->map(fn ($d) => [
                'defect_type' => $d->defect_type,
                'quantity' => (int) $d->quantity,
            ])->all() : [],
            'permissions' => $permissions,
            'registry' => $this->registry,
            'auditLogs' => $auditLogs,
            'shiftOptions' => $this->shiftOptions(),
        ]);
    }

    public function updateProduction(Request $request, ReceivingInspection $inspection)
    {
        return $this->saveSection($request, $inspection, 'PRODUCTION');
    }

    public function updateQc(Request $request, ReceivingInspection $inspection)
    {
        return $this->saveSection($request, $inspection, 'QC');
    }

    public function submitProduction(Request $request, ReceivingInspection $inspection)
    {
        return $this->submitSection($request, $inspection, 'PRODUCTION');
    }

    public function submitQc(Request $request, ReceivingInspection $inspection)
    {
        return $this->submitSection($request, $inspection, 'QC');
    }

    public function reopen(Request $request, ReceivingInspection $inspection, string $section)
    {
        $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        try {
            $this->service->reopen(auth()->user(), $inspection, $section, (string) $request->input('reason'));
        } catch (ReceivingInspectionException $e) {
            abort($e->status, $e->getMessage());
        }

        return redirect()->route('receiving-inspections.show', $inspection)->with('success', strtoupper($section) . ' section re-opened.');
    }

    public function destroy(Request $request, ReceivingInspection $inspection)
    {
        $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        try {
            $this->service->delete(auth()->user(), $inspection, (string) $request->input('reason'));
        } catch (ReceivingInspectionException $e) {
            abort($e->status, $e->getMessage());
        }

        return redirect()->route('receiving-inspections.index')->with('success', 'Inspection record deleted.');
    }

    public function audit(ReceivingInspection $inspection)
    {
        $this->assertAllowed($this->access->canViewAudit(auth()->user(), $inspection), 'You are not allowed to view this audit trail.');

        return view('receiving-inspections.audit', [
            'record' => $inspection,
            'logs' => $this->service->auditLogs($inspection),
            'registry' => $this->registry,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['area', 'date_from', 'date_to', 'prod_status', 'qc_status', 'has_qc_finding', 'search']);
        [$columns, $rows] = $this->service->exportRows($filters);

        if (empty($columns)) {
            $area = $filters['area'] ?? $this->registry->areas()[0];
            $columns = $this->registry->exportColumns($area);
        }

        $filename = 'receiving-inspections-' . date('Ymd-His');

        if (strtolower((string) $request->get('format', 'csv')) === 'xlsx') {
            return Excel::download(new ReceivingInspectionExport($columns, $rows), $filename . '.xlsx');
        }

        return response()->streamDownload(function () use ($columns, $rows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, array_map(fn ($column) => $column['label'] . ' / ' . $column['label_id'], $columns));
            foreach ($rows as $row) {
                $line = [];
                foreach ($columns as $column) {
                    $value = $row[$column['key']] ?? null;
                    $line[] = is_array($value) ? json_encode($value) : $value;
                }
                fputcsv($output, $line);
            }
            fclose($output);
        }, $filename . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function print(ReceivingInspection $inspection)
    {
        $permissions = $this->access->permissions(auth()->user(), $inspection);
        $canViewProduction = $permissions['canViewProduction'] ?? false;
        $canViewQc = $permissions['canViewQc'] ?? false;

        return view('receiving-inspections.print', [
            'record' => $inspection,
            'area' => $inspection->area,
            'production' => ($canViewProduction || $canViewQc) ? ($inspection->production_data ?? []) : [],
            'qc' => $canViewQc ? ($inspection->qc_data ?? []) : [],
            'defects' => $canViewQc ? $inspection->defects()->get()->map(fn ($d) => [
                'defect_type' => $d->defect_type,
                'quantity' => (int) $d->quantity,
            ])->all() : [],
            'permissions' => $permissions,
            'registry' => $this->registry,
        ]);
    }

    private function saveSection(Request $request, ReceivingInspection $inspection, string $section)
    {
        try {
            $this->service->updateSection(
                auth()->user(),
                $inspection,
                $section,
                $this->payload($request),
                $section === 'QC' ? $this->defects($request) : null
            );
        } catch (ReceivingInspectionException $e) {
            abort($e->status, $e->getMessage());
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('receiving-inspections.show', $inspection)->with('success', $section . ' draft saved.');
    }

    private function submitSection(Request $request, ReceivingInspection $inspection, string $section)
    {
        try {
            $this->service->submitSection(
                auth()->user(),
                $inspection,
                $section,
                $this->payload($request),
                $section === 'QC' ? $this->defects($request) : null
            );
        } catch (ReceivingInspectionException $e) {
            abort($e->status, $e->getMessage());
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('receiving-inspections.show', $inspection)->with('success', $section . ' section submitted.');
    }

    private function payload(Request $request): array
    {
        $fields = $request->input('fields');
        if (is_array($fields)) {
            return $fields;
        }

        return $request->except(['_token', '_method', 'defects', 'qc_defects', 'reason']);
    }

    private function defects(Request $request): ?array
    {
        if ($request->has('defects')) {
            $defects = $request->input('defects', []);
            return is_array($defects) ? $defects : [];
        }
        if ($request->has('qc_defects')) {
            $defects = $request->input('qc_defects', []);
            return is_array($defects) ? $defects : [];
        }

        return null;
    }

    private function shiftOptions(): array
    {
        try {
            $shifts = DataShift::query()->orderBy('shift')->pluck('shift')->unique()->values()->all();
            return !empty($shifts) ? $shifts : config('receiving_inspection.default_shifts');
        } catch (\Throwable $e) {
            return config('receiving_inspection.default_shifts');
        }
    }

    private function assertAllowed(bool $allowed, string $message): void
    {
        if (!$allowed) {
            abort(403, $message);
        }
    }
}
