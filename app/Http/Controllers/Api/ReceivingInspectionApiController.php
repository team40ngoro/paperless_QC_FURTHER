<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ReceivingInspectionException;
use App\Exports\ReceivingInspectionExport;
use App\Http\Controllers\Controller;
use App\Models\ReceivingInspection;
use App\Services\ReceivingInspection\ReceivingInspectionAccess;
use App\Services\ReceivingInspection\ReceivingInspectionRegistry;
use App\Services\ReceivingInspection\ReceivingInspectionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ReceivingInspectionApiController extends Controller
{
    public function __construct(
        private ReceivingInspectionService $service,
        private ReceivingInspectionRegistry $registry,
        private ReceivingInspectionAccess $access
    ) {
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, (int) $request->get('per_page', 25)));
        $filters = $request->only(['area', 'date_from', 'date_to', 'prod_status', 'qc_status', 'has_qc_finding', 'search']);
        $records = $this->service->query($filters)->paginate($perPage)->appends($request->query());

        return response()->json([
            'data' => $records->getCollection()->map(fn (ReceivingInspection $record) => $this->transform($record, $request->user()))->values(),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $record = $this->service->create($request->user(), $request->only(['area', 'inspection_date']));
        } catch (ReceivingInspectionException $e) {
            return $this->errorResponse($e);
        } catch (ValidationException $e) {
            return $this->validationResponse($e);
        }

        return response()->json($this->transform($record, $request->user()), 201);
    }

    public function show(Request $request, ReceivingInspection $inspection)
    {
        return response()->json($this->transform($inspection, $request->user()));
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

    public function reopen(Request $request, ReceivingInspection $inspection, ?string $section = null)
    {
        $section = strtoupper((string) ($section ?? $request->input('section', '')));
        if (!in_array($section, ['PRODUCTION', 'QC'], true)) {
            return response()->json(['message' => 'Validation failed.', 'errors' => ['section' => ['The section must be PRODUCTION or QC.']]], 400);
        }

        $validator = validator($request->all(), ['reason' => ['required', 'string', 'max:2000']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 400);
        }

        try {
            $record = $this->service->reopen($request->user(), $inspection, $section, (string) $request->input('reason'));
        } catch (ReceivingInspectionException $e) {
            return $this->errorResponse($e);
        }

        return response()->json($this->transform($record, $request->user()));
    }

    public function destroy(Request $request, ReceivingInspection $inspection)
    {
        $validator = validator($request->all(), ['reason' => ['required', 'string', 'max:2000']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 400);
        }

        try {
            $this->service->delete($request->user(), $inspection, (string) $request->input('reason'));
        } catch (ReceivingInspectionException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(['message' => 'Inspection record deleted.']);
    }

    public function audit(Request $request, ReceivingInspection $inspection)
    {
        if (!$this->access->canViewAudit($request->user(), $inspection)) {
            return response()->json(['message' => 'You are not allowed to view this audit trail.'], 403);
        }

        return response()->json([
            'data' => $this->service->auditLogs($inspection)->map(fn ($log) => [
                'id' => $log->id,
                'section' => $log->section,
                'action' => $log->action,
                'field_key' => $log->field_key,
                'old_value' => $log->old_value,
                'new_value' => $log->new_value,
                'actor_user_id' => $log->actor_user_id,
                'actor_role' => $log->actor_role,
                'reason' => $log->reason,
                'created_at' => $log->created_at?->toIso8601String(),
            ])->values(),
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

    private function saveSection(Request $request, ReceivingInspection $inspection, string $section)
    {
        try {
            $record = $this->service->updateSection(
                $request->user(),
                $inspection,
                $section,
                $this->payload($request),
                $section === 'QC' ? $this->defects($request) : null
            );
        } catch (ReceivingInspectionException $e) {
            return $this->errorResponse($e);
        } catch (ValidationException $e) {
            return $this->validationResponse($e);
        }

        return response()->json($this->transform($record, $request->user()));
    }

    private function submitSection(Request $request, ReceivingInspection $inspection, string $section)
    {
        try {
            $record = $this->service->submitSection(
                $request->user(),
                $inspection,
                $section,
                $this->payload($request),
                $section === 'QC' ? $this->defects($request) : null
            );
        } catch (ReceivingInspectionException $e) {
            return $this->errorResponse($e);
        } catch (ValidationException $e) {
            return $this->validationResponse($e);
        }

        return response()->json($this->transform($record, $request->user()));
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

    private function transform(ReceivingInspection $record, $user = null): array
    {
        $record->load(['defects', 'createdBy', 'prodFilledBy', 'qcFilledBy']);

        return array_merge($record->toArray(), [
            'production' => $record->production_data ?? [],
            'qc' => $record->qc_data ?? [],
            'defects' => $record->defects->map(fn ($d) => [
                'defect_type' => $d->defect_type,
                'quantity' => (int) $d->quantity,
            ])->values()->all(),
            'is_complete' => $record->is_complete,
            'permissions' => $this->access->permissions($user ?? auth()->user(), $record),
        ]);
    }

    private function errorResponse(ReceivingInspectionException $e)
    {
        return response()->json([
            'message' => $e->getMessage(),
            'rejectedFields' => $e->rejectedFields,
            'unknownFields' => $e->unknownFields,
        ], $e->status);
    }

    private function validationResponse(ValidationException $e)
    {
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 400);
    }
}
