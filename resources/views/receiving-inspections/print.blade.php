<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $registry->areaLabel($area) }} Inspection · {{ optional($record->inspection_date)->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 24px; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 14px; margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #444; }
        .meta { margin-bottom: 16px; }
        .meta td { padding: 2px 12px 2px 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #999; padding: 5px 7px; text-align: left; vertical-align: top; }
        th { background: #eee; }
        .value { font-weight: bold; }
        .muted { color: #777; }
        .badge { border: 1px solid #666; padding: 1px 5px; border-radius: 3px; }
        .toolbar { margin-bottom: 16px; }
        @media print { .toolbar { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Print</button>
        <a href="{{ route('receiving-inspections.show', $record) }}">Back</a>
    </div>

    @php
        $showProduction = $permissions['canViewProduction'] ?? false;
        $showQc = $permissions['canViewQc'] ?? false;
    @endphp

    <h1>{{ $registry->title($area) }}</h1>
    <div class="meta">
        <table>
            <tr>
                <td><strong>Area</strong></td>
                <td>{{ $registry->areaLabel($area) }}</td>
                <td><strong>Day / Date</strong></td>
                <td>{{ $record->inspection_date ? $registry->weekdayId($record->inspection_date->format('Y-m-d')) : '—' }}, {{ optional($record->inspection_date)->format('Y-m-d') }}</td>
            </tr>
            @if($showProduction || $showQc)
                <tr>
                    <td><strong>QC form</strong></td>
                    <td>{{ $showQc ? $record->qc_form_code : '—' }}</td>
                    <td><strong>Production form</strong></td>
                    <td>{{ $showProduction ? $record->prod_form_code : '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Production status</strong></td>
                    <td><span class="badge">{{ $showProduction ? $record->prod_status : '—' }}</span></td>
                    <td><strong>QC status</strong></td>
                    <td><span class="badge">{{ $showQc ? $record->qc_status : '—' }}</span></td>
                </tr>
            @endif
        </table>
    </div>

    @if(!$showProduction && !$showQc)
        <p class="muted">You do not have access to view the inspection sections.</p>
    @endif

    @foreach(['PRODUCTION' => $production, 'QC' => $qc] as $section => $data)
        @if(($section === 'PRODUCTION' && !$showProduction) || ($section === 'QC' && !$showQc))
            @continue
        @endif
        <h2>{{ $section }} section ({{ $section === 'PRODUCTION' ? $record->prod_form_code : $record->qc_form_code }})</h2>
        @if($section === 'QC' && $registry->legend($area))
            <p class="muted">{{ $registry->legend($area) }}</p>
        @endif
        <table>
            <thead><tr><th style="width: 42%;">Field</th><th>Value</th></tr></thead>
            <tbody>
                @foreach($registry->fields($area, $section) as $field)
                    @if($field['type'] === 'repeatable')
                        @continue
                    @endif
                    @php
                        $value = $data[$field['key']] ?? null;
                        if ($field['type'] === 'ok_not_ok') {
                            $display = $value === 'OK' ? '✔ OK' : ($value === 'NOT_OK' ? '✘ NOT OK' : '—');
                        } elseif ($field['type'] === 'boolean') {
                            $display = $value ? 'Yes' : 'No';
                        } else {
                            $display = ($value === null || $value === '') ? '—' : $value;
                        }
                    @endphp
                    <tr>
                        <td>{{ $field['label_id'] ?? $field['label'] }} <span class="muted">({{ $field['key'] }})</span></td>
                        <td class="value">{{ $display }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @if($area === 'SHOESTRING' && $showQc)
        <h2>QC defects</h2>
        <table>
            <thead><tr><th>Defect type</th><th>Quantity</th></tr></thead>
            <tbody>
                @forelse($defects as $defect)
                    <tr><td>{{ $defect['defect_type'] }}</td><td>{{ $defect['quantity'] }}</td></tr>
                @empty
                    <tr><td colspan="2" class="muted">No defects found.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <p class="muted" style="margin-top: 24px;">Generated {{ now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') }} WIB</p>
</body>
</html>
