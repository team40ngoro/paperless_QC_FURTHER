<?php

namespace App\Services\ReceivingInspection;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ReceivingInspectionRegistry
{
    public const SECTIONS = ['PRODUCTION', 'QC'];

    public function areas(): array
    {
        return array_keys(config('receiving_inspection.areas', []));
    }

    public function hasArea(string $area): bool
    {
        return array_key_exists($area, config('receiving_inspection.areas', []));
    }

    public function area(string $area): array
    {
        $definition = config('receiving_inspection.areas.' . $area);
        if (!$definition) {
            throw new InvalidArgumentException('Unknown inspection area: ' . $area);
        }
        return $definition;
    }

    public function areaLabel(string $area): string
    {
        return $this->area($area)['label'] ?? $area;
    }

    public function title(string $area): string
    {
        return $this->area($area)['title'] ?? '';
    }

    public function formCodes(string $area): array
    {
        $definition = $this->area($area);
        return [
            'qc_form_code' => $definition['qc_form_code'] ?? null,
            'prod_form_code' => $definition['prod_form_code'] ?? null,
        ];
    }

    public function legend(string $area): ?string
    {
        return $this->area($area)['legend'] ?? null;
    }

    public function fields(string $area, ?string $section = null): array
    {
        $fields = $this->area($area)['fields'] ?? [];
        if ($section === null) {
            return $fields;
        }
        return array_values(array_filter($fields, fn (array $field) => $field['section'] === $section));
    }

    public function field(string $area, string $key): ?array
    {
        foreach ($this->fields($area) as $field) {
            if ($field['key'] === $key) {
                return $field;
            }
        }
        return null;
    }

    public function fieldKeys(string $area, ?string $section = null): array
    {
        return array_map(fn (array $field) => $field['key'], $this->fields($area, $section));
    }

    public function allowedFields(string $role, string $area, string $section): array
    {
        $allowed = [];
        foreach ($this->fields($area, $section) as $field) {
            if (in_array($role, $field['writableBy'] ?? [], true)) {
                $allowed[] = $field['key'];
            }
        }
        return $allowed;
    }

    public function defaults(string $area, string $section): array
    {
        $defaults = [];
        foreach ($this->fields($area, $section) as $field) {
            if (array_key_exists('default', $field)) {
                $defaults[$field['key']] = $field['default'];
            }
        }
        return $defaults;
    }

    public function validate(string $area, string $section, array $data, bool $forSubmit): array
    {
        $normalized = [];
        $rules = [];
        $definitions = $this->fields($area, $section);

        foreach ($definitions as $field) {
            $key = $field['key'];
            if ($field['type'] === 'repeatable') {
                continue;
            }

            $provided = array_key_exists($key, $data);
            if ($provided) {
                $normalized[$key] = $this->normalizeValue($field, $data[$key]);
            }

            if ($forSubmit && ($field['required'] ?? false) && empty($field['required_when']) && empty($field['required_unless']) && empty($field['required_if_any_not_ok'])) {
                $rules[$key] = array_merge(['required', 'nullable'], $this->typeRules($field));
            } else {
                $rules[$key] = array_merge(['sometimes', 'nullable'], $this->typeRules($field));
            }
        }

        $validator = Validator::make($normalized, $rules);
        $validator->after(function ($validator) use ($definitions, $normalized, $forSubmit, $area, $section, $data) {
            foreach ($definitions as $field) {
                if ($field['type'] === 'repeatable') {
                    continue;
                }

                $key = $field['key'];
                $value = $normalized[$key] ?? null;

                if ($forSubmit && !empty($field['required_when'])) {
                    $condition = $field['required_when'];
                    if ($this->conditionMatches($normalized, $condition) && $this->isEmpty($value)) {
                        $validator->errors()->add($key, $field['label'] . ' is required.');
                    }
                }

                if ($forSubmit && !empty($field['required_unless'])) {
                    $condition = $field['required_unless'];
                    if (!$this->conditionMatches($normalized, $condition) && $this->isEmpty($value)) {
                        $validator->errors()->add($key, $field['label'] . ' is required.');
                    }
                }

                if ($forSubmit && !empty($field['required_if_any_not_ok']) && $this->sectionHasNotOk($definitions, $normalized) && $this->isEmpty($value)) {
                    $validator->errors()->add($key, $field['label'] . ' is required when any QC check is NOT OK.');
                }

                if (!empty($field['validation']['after_or_equal_field']) && !$this->isEmpty($value)) {
                    $otherKey = $field['validation']['after_or_equal_field'];
                    $otherValue = $normalized[$otherKey] ?? null;
                    if (!$this->isEmpty($otherValue)) {
                        try {
                            if (Carbon::parse($value)->lt(Carbon::parse($otherValue))) {
                                $validator->errors()->add($key, $field['label'] . ' must be greater than or equal to ' . $otherKey . '.');
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $normalized;
    }

    public function normalizeValue(array $field, $value)
    {
        if ($value === null) {
            return null;
        }

        switch ($field['type']) {
            case 'ok_not_ok':
                return $this->normalizeOkNotOk($value);
            case 'boolean':
                return $this->toBoolean($value);
            case 'decimal':
            case 'temp_c':
                if (is_string($value)) {
                    $value = str_replace(',', '.', trim($value));
                }
                return $value === '' ? null : (float) $value;
            case 'integer':
                return $value === '' ? null : (int) $value;
            case 'enum':
            case 'status':
                return is_string($value) ? strtoupper(trim($value)) : $value;
            case 'string':
            case 'text':
            case 'shift':
            case 'date':
            case 'time':
                return is_string($value) ? (trim($value) === '' ? null : trim($value)) : $value;
            default:
                return $value;
        }
    }

    public function normalizeOkNotOk($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'OK' : 'NOT_OK';
        }

        $normalized = strtolower(trim((string) $value));
        $okValues = ['ok', 'v', '✓', '✓ oke', 'oke', '1', 'true'];
        $notOkValues = ['not_ok', 'not ok', 'x', '✘', '✘ tidak oke', 'tidak oke', '0', 'false'];

        if (in_array($normalized, $okValues, true)) {
            return 'OK';
        }
        if (in_array($normalized, $notOkValues, true)) {
            return 'NOT_OK';
        }

        return strtoupper($normalized);
    }

    public function toBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value === 1;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function hasQcFinding(string $area, array $qcData, int $defectCount = 0): bool
    {
        foreach ($this->fields($area, 'QC') as $field) {
            if (($field['type'] ?? null) !== 'ok_not_ok') {
                continue;
            }
            $value = $qcData[$field['key']] ?? null;
            if ($this->normalizeOkNotOk($value) === 'NOT_OK') {
                return true;
            }
        }

        if ($area === 'SHOESTRING' && !$this->toBoolean($qcData['qc_no_defects'] ?? false) && $defectCount > 0) {
            return true;
        }

        return false;
    }

    public function weekdayId(string $date): string
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $days[Carbon::parse($date)->format('l')] ?? '';
    }

    public function exportColumns(string $area): array
    {
        $columns = [
            ['key' => 'inspection_date', 'label' => 'Date', 'label_id' => 'Tanggal'],
            ['key' => 'area', 'label' => 'Area', 'label_id' => 'Area'],
            ['key' => 'qc_form_code', 'label' => 'QC form', 'label_id' => 'Form QC'],
            ['key' => 'prod_form_code', 'label' => 'Production form', 'label_id' => 'Form Produksi'],
            ['key' => 'prod_status', 'label' => 'Production status', 'label_id' => 'Status Produksi'],
            ['key' => 'qc_status', 'label' => 'QC status', 'label_id' => 'Status QC'],
        ];

        foreach ($this->fields($area) as $field) {
            $columns[] = [
                'key' => $field['key'],
                'label' => $field['label'],
                'label_id' => $field['label_id'] ?? $field['label'],
                'section' => $field['section'],
            ];
        }

        return $columns;
    }

    private function typeRules(array $field): array
    {
        $validation = $field['validation'] ?? [];
        $maxLength = $validation['maxLength'] ?? null;
        $min = $validation['min'] ?? null;
        $max = $validation['max'] ?? null;

        switch ($field['type']) {
            case 'string':
                return array_values(array_filter(['string', $maxLength ? 'max:' . $maxLength : null]));
            case 'text':
                return array_values(array_filter(['string', $maxLength ? 'max:' . $maxLength : null]));
            case 'decimal':
            case 'temp_c':
                return array_values(array_filter([
                    'numeric',
                    $min !== null ? 'min:' . $min : null,
                    $max !== null ? 'max:' . $max : null,
                ]));
            case 'integer':
                return array_values(array_filter(['integer', $min !== null ? 'min:' . $min : null]));
            case 'date':
                return ['date_format:Y-m-d'];
            case 'time':
                return ['date_format:H:i'];
            case 'enum':
                return ['in:' . implode(',', $validation['enumValues'] ?? [])];
            case 'ok_not_ok':
                return ['in:OK,NOT_OK'];
            case 'status':
                return ['in:ACCEPTED,REJECTED'];
            case 'boolean':
                return ['boolean'];
            case 'shift':
                return ['string', 'max:50'];
            default:
                return [];
        }
    }

    private function conditionMatches(array $data, array $condition): bool
    {
        $key = $condition['field'] ?? null;
        $expected = $condition['equals'] ?? null;
        if ($key === null) {
            return false;
        }

        $actual = $data[$key] ?? null;
        if (is_bool($expected)) {
            return $this->toBoolean($actual) === $expected;
        }

        return (string) $actual === (string) $expected;
    }

    private function sectionHasNotOk(array $definitions, array $data): bool
    {
        foreach ($definitions as $field) {
            if (($field['type'] ?? null) !== 'ok_not_ok') {
                continue;
            }
            if ($this->normalizeOkNotOk($data[$field['key']] ?? null) === 'NOT_OK') {
                return true;
            }
        }
        return false;
    }

    private function isEmpty($value): bool
    {
        return $value === null || $value === '';
    }
}
