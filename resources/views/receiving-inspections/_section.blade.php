@php
    $status = $section === 'PRODUCTION' ? $record->prod_status : $record->qc_status;
    $editable = $section === 'PRODUCTION' ? ($permissions['canEditProduction'] ?? false) : ($permissions['canEditQc'] ?? false);
    $canSubmit = $section === 'PRODUCTION' ? ($permissions['canSubmitProduction'] ?? false) : ($permissions['canSubmitQc'] ?? false);
    $formCode = $section === 'PRODUCTION' ? $record->prod_form_code : $record->qc_form_code;
    $legend = $registry->legend($area);
@endphp

<div class="card {{ $section === 'PRODUCTION' ? 'card-info' : 'card-success' }} card-outline mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas {{ $section === 'PRODUCTION' ? 'fa-industry' : 'fa-clipboard-check' }} mr-1"></i>
            {{ $section === 'PRODUCTION' ? 'Production' : 'QC' }}
            <span class="badge badge-light border ml-2">{{ $formCode }}</span>
            <span class="badge {{ $status === 'SUBMITTED' ? 'badge-success' : 'badge-warning' }} ml-1">{{ $status }}</span>
            @if(!$editable)
                <i class="fas fa-lock text-muted ml-2" title="Only the owning role can edit this section"></i>
            @endif
        </h3>
        <div class="card-tools">
            <span class="small text-muted">
                {{ $section === 'PRODUCTION' ? 'Writer: Production Input Checker' : 'Writer: QC Auditor' }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}" class="receiving-section-form">
        @csrf
        @method('PATCH')

        <div class="card-body">
            @if($section === 'QC' && $legend)
                <div class="alert alert-info py-2 small">
                    <strong>Legend:</strong> {{ $legend }}
                </div>
            @endif

            <div class="row">
                @foreach($registry->fields($area, $section) as $field)
                    @if($field['type'] === 'repeatable')
                        @continue
                    @endif
                    @if($area === 'SHOESTRING' && $section === 'QC' && $field['key'] === 'qc_no_defects')
                        @continue
                    @endif
                    <div class="col-md-6">
                        @include('receiving-inspections._field', [
                            'field' => $field,
                            'value' => $data[$field['key']] ?? null,
                            'readonly' => !$editable,
                            'section' => $section,
                            'production' => $production,
                            'registry' => $registry,
                            'shiftOptions' => $shiftOptions,
                            'area' => $area,
                            'record' => $record,
                        ])
                    </div>
                @endforeach
            </div>

            @if($area === 'SHOESTRING' && $section === 'QC')
                @include('receiving-inspections._defects', [
                    'defects' => $defects,
                    'readonly' => !$editable,
                ])
            @endif
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="small text-muted">
                @if($record->{'prod_status'} === 'SUBMITTED' && $section === 'QC' && $record->qc_status === 'DRAFT')
                    Production section is already submitted.
                @endif
                @if($record->{'qc_status'} === 'SUBMITTED' && $section === 'PRODUCTION' && $record->prod_status === 'DRAFT')
                    QC section is already submitted.
                @endif
            </span>
            @if($editable)
                <div>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-save mr-1"></i> Save draft
                    </button>
                    @if($canSubmit)
                        <button type="button" class="btn btn-primary"
                                onclick="submitReceivingForm(this, '{{ $submitAction }}')">
                            <i class="fas fa-paper-plane mr-1"></i> Submit
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </form>
</div>
