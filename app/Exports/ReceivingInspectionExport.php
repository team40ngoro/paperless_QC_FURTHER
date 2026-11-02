<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceivingInspectionExport implements FromArray, WithHeadings
{
    public function __construct(private array $columns, private array $rows)
    {
    }

    public function headings(): array
    {
        return array_map(fn (array $column) => $column['label'] . ' / ' . $column['label_id'], $this->columns);
    }

    public function array(): array
    {
        return array_map(function (array $row) {
            return array_map(function (array $column) use ($row) {
                $value = $row[$column['key']] ?? null;
                return is_array($value) ? json_encode($value) : $value;
            }, $this->columns);
        }, $this->rows);
    }
}
