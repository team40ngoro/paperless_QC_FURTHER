<?php

namespace App\Services\ReceivingInspection;

use App\Exceptions\ReceivingInspectionException;
use App\Models\InspectionAuditLog;
use App\Models\ReceivingInspection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReceivingInspectionService
{
    public function __construct(
        private ReceivingInspectionRegistry $registry,
        private ReceivingInspectionAccess $access
    ) {
    }

    public function create(User $user, array $payload): ReceivingInspection
    {
        $this->access->assertCanCreate($user);

        $area = strtoupper(trim((string) ($payload['area'] ?? '')));
        $date = $payload['inspection_date'] ?? null;

        $validator = Validator::make(
            ['area' => $area, 'inspection_date' => $date],
            [
                'area' => ['required', 'string', 'in:' . implode(',', $this->registry->areas())],
                'inspection_date' => ['required', 'date_format:Y-m-d'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $codes = $this->registry->formCodes($area);

        return DB::transaction(function () use ($user, $area, $date, $codes) {
            $record = ReceivingInspection::create([
                'area' => $area,
                'inspection_date' => $date,
                'qc_form_code' => $codes['qc_form_code'],
                'prod_form_code' => $codes['prod_form_code'],
                'prod_status' => 'DRAFT',
                'qc_status' => 'DRAFT',
                'production_data' => $this->registry->defaults($area, 'PRODUCTION'),
                'qc_data' => $this->registry->defaults($area, 'QC'),
                'created_by' => $user->id,
                'has_qc_finding' => false,
            ]);

            $this->audit($record, 'SYSTEM', 'CREATE', null, null, null, $user);

            return $record;
        });
    }

    public function updateSection(User $user, ReceivingInspection $record, string $section, array $payload, ?array $defects = null): ReceivingInspection
    {
        $section = strtoupper($section);
        $this->assertSection($section);
        $this->access->assertCanEditSection($user, $record, $section);

        $area = $record->area;
        $areaKeys = $this->registry->fieldKeys($area);
        $sectionKeys = $this->registry->fieldKeys($area, $section);

        $unknown = array_values(array_diff(array_keys($payload), $areaKeys));
        if (!empty($unknown)) {
            throw ReceivingInspectionException::unknownFields($unknown);
        }

        $role = $this->access->roleFor($user);
        $allowed = $this->registry->allowedFields($role, $area, $section);
        $rejected = array_values(array_unique(array_merge(
            array_diff(array_keys($payload), $sectionKeys),
            array_diff(array_keys($payload), $allowed)
        )));

        if (!empty($rejected)) {
            throw ReceivingInspectionException::rejectedFields($rejected);
        }

        $defectRows = $defects;
        if ($defectRows === null && array_key_exists('qc_defects', $payload)) {
            $defectRows = is_array($payload['qc_defects']) ? $payload['qc_defects'] : [];
        }

        $validated = $this->registry->validate($area, $section, $payload, false);

        if ($section === 'QC' && $area === 'SHOESTRING') {
            $noDefects = $this->registry->toBoolean($validated['qc_no_defects'] ?? ($record->qc_data['qc_no_defects'] ?? false));
            if ($defectRows !== null) {
                $defectRows = $this->validateDefects($defectRows);
            }
            if ($noDefects) {
                $defectRows = [];
            }
        }

        return DB::transaction(function () use ($user, $record, $section, $area, $validated, $defectRows) {
            $column = $section === 'PRODUCTION' ? 'production_data' : 'qc_data';
            $statusColumn = $section === 'PRODUCTION' ? 'prod_status' : 'qc_status';
            $filledColumn = $section === 'PRODUCTION' ? 'prod_filled_by' : 'qc_filled_by';

            $oldData = $record->{$column} ?? [];
            $newData = array_merge($oldData, $validated);

            if ($section === 'PRODUCTION' && !$this->registry->toBoolean($newData['prod_is_meat_rm'] ?? false)) {
                foreach ($this->registry->fieldKeys($area, 'PRODUCTION') as $key) {
                    if (str_starts_with($key, 'prod_') && !in_array($key, ['prod_is_meat_rm'], true)) {
                        $field = $this->registry->field($area, $key);
                        if (!empty($field['visibleIf']) && ($field['visibleIf']['field'] ?? null) === 'prod_is_meat_rm') {
                            $newData[$key] = null;
                        }
                    }
                }
            }

            $record->{$column} = $newData;
            $record->{$filledColumn} = $user->id;
            if ($section === 'QC') {
                $defectCount = 0;
                if ($area === 'SHOESTRING') {
                    $defectCount = $defectRows !== null ? count($defectRows) : $record->defects()->count();
                    if ($this->registry->toBoolean($newData['qc_no_defects'] ?? false)) {
                        $defectCount = 0;
                    }
                }
                $record->has_qc_finding = $this->registry->hasQcFinding($area, $newData, $defectCount);
            }
            $record->save();

            $changedKeys = array_unique(array_merge(array_keys($validated), array_keys($oldData), array_keys($newData)));
            foreach ($changedKeys as $key) {
                $old = $oldData[$key] ?? null;
                $new = $newData[$key] ?? null;
                if ($old !== $new) {
                    $this->audit($record, $section, 'UPDATE', $key, $old, $new, $user);
                }
            }

            if ($section === 'QC' && $area === 'SHOESTRING' && $defectRows !== null) {
                $oldDefects = $record->defects()->get(['defect_type', 'quantity'])->map(fn ($d) => [
                    'defect_type' => $d->defect_type,
                    'quantity' => (int) $d->quantity,
                ])->all();

                $record->defects()->delete();
                foreach ($defectRows as $row) {
                    $record->defects()->create([
                        'defect_type' => $row['defect_type'],
                        'quantity' => (int) $row['quantity'],
                    ]);
                }

                if ($oldDefects !== $defectRows) {
                    $this->audit($record, $section, 'UPDATE', 'qc_defects', $oldDefects, $defectRows, $user);
                }
            }

            return $record->fresh(['defects']);
        });
    }

    public function submitSection(User $user, ReceivingInspection $record, string $section, array $payload = [], ?array $defects = null): ReceivingInspection
    {
        $section = strtoupper($section);
        $this->assertSection($section);
        $this->access->assertCanEditSection($user, $record, $section);

        if (!empty($payload) || $defects !== null) {
            $record = $this->updateSection($user, $record, $section, $payload, $defects);
        }

        $column = $section === 'PRODUCTION' ? 'production_data' : 'qc_data';
        $statusColumn = $section === 'PRODUCTION' ? 'prod_status' : 'qc_status';
        $filledColumn = $section === 'PRODUCTION' ? 'prod_filled_by' : 'qc_filled_by';
        $submittedColumn = $section === 'PRODUCTION' ? 'prod_submitted_at' : 'qc_submitted_at';

        $data = $record->{$column} ?? [];
        $this->registry->validate($record->area, $section, $data, true);

        if ($section === 'QC' && $record->area === 'SHOESTRING') {
            $noDefects = $this->registry->toBoolean($data['qc_no_defects'] ?? false);
            if (!$noDefects && $record->defects()->count() === 0) {
                throw ValidationException::withMessages([
                    'qc_defects' => 'At least one defect row is required, or tick "No defects found".',
                ]);
            }
        }

        return DB::transaction(function () use ($user, $record, $section, $statusColumn, $filledColumn, $submittedColumn, $column) {
            $oldStatus = $record->{$statusColumn};
            if ($oldStatus === 'SUBMITTED') {
                throw ReceivingInspectionException::conflict('The ' . $section . ' section has already been submitted.');
            }

            $record->{$statusColumn} = 'SUBMITTED';
            $record->{$filledColumn} = $user->id;
            $record->{$submittedColumn} = Carbon::now();
            if ($section === 'QC') {
                $defectCount = $record->area === 'SHOESTRING' ? $record->defects()->count() : 0;
                $record->has_qc_finding = $this->registry->hasQcFinding($record->area, $record->{$column} ?? [], $defectCount);
            }
            $record->save();

            $this->audit($record, $section, 'SUBMIT', '__section_status', $oldStatus, 'SUBMITTED', $user);

            return $record->fresh(['defects']);
        });
    }

    public function reopen(User $user, ReceivingInspection $record, string $section, string $reason): ReceivingInspection
    {
        $this->access->assertCanReopen($user);
        $section = strtoupper($section);
        $this->assertSection($section);

        $reason = trim($reason);
        if ($reason === '') {
            throw ReceivingInspectionException::badRequest('A reason is required to re-open a section.');
        }

        $statusColumn = $section === 'PRODUCTION' ? 'prod_status' : 'qc_status';
        $submittedColumn = $section === 'PRODUCTION' ? 'prod_submitted_at' : 'qc_submitted_at';

        if ($record->{$statusColumn} !== 'SUBMITTED') {
            throw ReceivingInspectionException::conflict('Only a submitted section can be re-opened.');
        }

        return DB::transaction(function () use ($user, $record, $section, $reason, $statusColumn, $submittedColumn) {
            $record->{$statusColumn} = 'DRAFT';
            $record->{$submittedColumn} = null;
            $record->save();

            $this->audit($record, $section, 'REOPEN', '__section_status', 'SUBMITTED', 'DRAFT', $user, $reason);

            return $record->fresh(['defects']);
        });
    }

    public function delete(User $user, ReceivingInspection $record, string $reason): void
    {
        $this->access->assertCanDelete($user);

        $reason = trim($reason);
        if ($reason === '') {
            throw ReceivingInspectionException::badRequest('A reason is required to delete a record.');
        }

        DB::transaction(function () use ($user, $record, $reason) {
            $this->audit($record, 'SYSTEM', 'DELETE', null, null, null, $user, $reason);
            $record->delete();
        });
    }

    public function query(array $filters = []): Builder
    {
        $query = ReceivingInspection::query();

        if (!empty($filters['area'])) {
            $query->where('area', strtoupper($filters['area']));
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('inspection_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('inspection_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['prod_status'])) {
            $query->where('prod_status', strtoupper($filters['prod_status']));
        }
        if (!empty($filters['qc_status'])) {
            $query->where('qc_status', strtoupper($filters['qc_status']));
        }
        if (array_key_exists('has_qc_finding', $filters) && $filters['has_qc_finding'] !== null && $filters['has_qc_finding'] !== '') {
            $query->where('has_qc_finding', filter_var($filters['has_qc_finding'], FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('area', 'like', '%' . $search . '%')
                    ->orWhere('uuid', 'like', '%' . $search . '%')
                    ->orWhere('production_data', 'like', '%' . $search . '%')
                    ->orWhere('qc_data', 'like', '%' . $search . '%');
            });
        }

        return $query->orderByDesc('inspection_date')->orderByDesc('id');
    }

    public function auditLogs(ReceivingInspection $record)
    {
        return $record->auditLogs()->with('actor')->get();
    }

    public function exportRows(array $filters = []): array
    {
        $records = $this->query($filters)->get();
        $columns = [];
        foreach ($records as $record) {
            $columns = $this->registry->exportColumns($record->area);
            break;
        }

        $rows = [];
        foreach ($records as $record) {
            $production = $record->production_data ?? [];
            $qc = $record->qc_data ?? [];
            $defects = $record->defects()->get(['defect_type', 'quantity'])->map(fn ($d) => [
                'defect_type' => $d->defect_type,
                'quantity' => (int) $d->quantity,
            ])->all();

            $row = [
                'inspection_date' => optional($record->inspection_date)->format('Y-m-d'),
                'area' => $record->area,
                'qc_form_code' => $record->qc_form_code,
                'prod_form_code' => $record->prod_form_code,
                'prod_status' => $record->prod_status,
                'qc_status' => $record->qc_status,
            ];

            foreach ($this->registry->fields($record->area) as $field) {
                $key = $field['key'];
                if ($key === 'qc_defects') {
                    $row[$key] = json_encode($defects);
                } elseif ($field['section'] === 'PRODUCTION') {
                    $row[$key] = $production[$key] ?? null;
                } else {
                    $row[$key] = $qc[$key] ?? null;
                }
            }

            $rows[] = $row;
        }

        return [$columns, $rows];
    }

    private function validateDefects(array $rows): array
    {
        $rows = array_values(array_filter($rows, function ($row) {
            return is_array($row) && (($row['defect_type'] ?? '') !== '' || ($row['quantity'] ?? '') !== '');
        }));

        $validator = Validator::make(['qc_defects' => $rows], [
            'qc_defects' => ['array'],
            'qc_defects.*.defect_type' => ['required', 'string', 'max:255'],
            'qc_defects.*.quantity' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return array_map(fn ($row) => [
            'defect_type' => trim((string) $row['defect_type']),
            'quantity' => (int) $row['quantity'],
        ], $rows);
    }

    private function audit(ReceivingInspection $record, string $section, string $action, ?string $fieldKey, $oldValue, $newValue, User $actor, ?string $reason = null): void
    {
        InspectionAuditLog::create([
            'receiving_inspection_id' => $record->id,
            'area' => $record->area,
            'section' => $section,
            'action' => $action,
            'field_key' => $fieldKey,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'actor_user_id' => $actor->id,
            'actor_role' => $this->access->roleFor($actor),
            'reason' => $reason,
        ]);
    }

    private function assertSection(string $section): void
    {
        if (!in_array($section, ReceivingInspectionRegistry::SECTIONS, true)) {
            throw ReceivingInspectionException::badRequest('Unknown section: ' . $section);
        }
    }
}
