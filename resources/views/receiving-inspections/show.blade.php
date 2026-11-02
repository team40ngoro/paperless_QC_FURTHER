@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-7">
                    <h1>
                        {{ $registry->areaLabel($area) }} Inspection
                        <span class="badge {{ $record->is_complete ? 'badge-success' : 'badge-warning' }} align-middle">
                            {{ $record->is_complete ? 'COMPLETE' : 'IN PROGRESS' }}
                        </span>
                    </h1>
                </div>
                <div class="col-sm-5">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('receiving-inspections.index') }}">Incoming Inspection</a></li>
                        <li class="breadcrumb-item active">{{ $registry->areaLabel($area) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card card-outline card-secondary mb-3">
                <div class="card-body py-2">
                    <div class="row small">
                        <div class="col-md-3"><strong>Date:</strong> {{ optional($record->inspection_date)->format('Y-m-d') }} ({{ $record->inspection_date ? $registry->weekdayId($record->inspection_date->format('Y-m-d')) : '—' }})</div>
                        <div class="col-md-2"><strong>Area:</strong> {{ $registry->areaLabel($area) }}</div>
                        @if($permissions['canViewQc'] ?? false)
                            <div class="col-md-2"><strong>QC form:</strong> {{ $record->qc_form_code }}</div>
                        @endif
                        @if($permissions['canViewProduction'] ?? false)
                            <div class="col-md-2"><strong>Production form:</strong> {{ $record->prod_form_code }}</div>
                        @endif
                        <div class="col-md-3 text-md-right">
                            <a href="{{ route('receiving-inspections.print', $record) }}" target="_blank" class="btn btn-xs btn-outline-secondary">
                                <i class="fas fa-print mr-1"></i> Print
                            </a>
                            @if($permissions['canViewAudit'] ?? false)
                                <a href="{{ route('receiving-inspections.audit', $record) }}" class="btn btn-xs btn-outline-info">
                                    <i class="fas fa-history mr-1"></i> Audit trail
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row small mt-1 text-muted">
                        @if($permissions['canViewProduction'] ?? false)
                            <div class="col-md-6">
                                Production filled by {{ $record->prodFilledBy->name ?? '—' }}
                                @if($record->prod_submitted_at) · submitted {{ $record->prod_submitted_at->timezone('Asia/Jakarta')->format('Y-m-d H:i') }} @endif
                            </div>
                        @endif
                        @if($permissions['canViewQc'] ?? false)
                            <div class="col-md-6">
                                QC filled by {{ $record->qcFilledBy->name ?? '—' }}
                                @if($record->qc_submitted_at) · submitted {{ $record->qc_submitted_at->timezone('Asia/Jakarta')->format('Y-m-d H:i') }} @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $showProduction = $permissions['canViewProduction'] ?? false;
                $showQc = $permissions['canViewQc'] ?? false;
            @endphp

            @if(!$showProduction && !$showQc)
                <div class="alert alert-warning">
                    You do not have access to view the inspection sections.
                </div>
            @endif

            <div class="row">
                @if($showProduction)
                    <div class="col-lg-{{ $showQc ? '6' : '12' }}">
                        @include('receiving-inspections._section', [
                            'section' => 'PRODUCTION',
                            'area' => $area,
                            'record' => $record,
                            'data' => $production,
                            'production' => $production,
                            'defects' => [],
                            'permissions' => $permissions,
                            'registry' => $registry,
                            'shiftOptions' => $shiftOptions,
                            'formAction' => route('receiving-inspections.production.update', $record),
                            'submitAction' => route('receiving-inspections.production.submit', $record),
                        ])
                    </div>
                @endif
                @if($showQc)
                    <div class="col-lg-{{ $showProduction ? '6' : '12' }}">
                        @include('receiving-inspections._section', [
                            'section' => 'QC',
                            'area' => $area,
                            'record' => $record,
                            'data' => $qc,
                            'production' => $production,
                            'defects' => $defects,
                            'permissions' => $permissions,
                            'registry' => $registry,
                            'shiftOptions' => $shiftOptions,
                            'formAction' => route('receiving-inspections.qc.update', $record),
                            'submitAction' => route('receiving-inspections.qc.submit', $record),
                        ])
                    </div>
                @endif
            </div>

            @if($permissions['canReopen'] ?? false)
                <div class="card card-outline card-warning">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-unlock mr-1"></i> Admin actions</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Re-open a submitted section</h6>
                                @foreach(['PRODUCTION', 'QC'] as $section)
                                    @php $status = $section === 'PRODUCTION' ? $record->prod_status : $record->qc_status; @endphp
                                    @if($status === 'SUBMITTED')
                                        <form method="POST" action="{{ route('receiving-inspections.reopen', [$record, $section]) }}" class="mb-2">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="reason" class="form-control" placeholder="Reason for re-opening {{ $section }}" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-warning" type="submit">Re-open {{ $section }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <h6>Delete record</h6>
                                <form method="POST" action="{{ route('receiving-inspections.destroy', $record) }}"
                                      onsubmit="return confirm('Soft delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="reason" class="form-control" placeholder="Reason for deleting" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger" type="submit">Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(($permissions['canViewAudit'] ?? false) && $auditLogs->isNotEmpty())
                <div class="card card-outline card-info">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-history mr-1"></i> Recent audit trail</h3></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="bg-light"><tr><th>Time</th><th>Section</th><th>Action</th><th>Field</th><th>Actor</th><th>Reason</th></tr></thead>
                                <tbody>
                                    @foreach($auditLogs->take(15) as $log)
                                        <tr>
                                            <td>{{ $log->created_at?->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->section }}</td>
                                            <td><span class="badge badge-secondary">{{ $log->action }}</span></td>
                                            <td>{{ $log->field_key ?? '—' }}</td>
                                            <td>{{ $log->actor->name ?? $log->actor_user_id ?? '—' }} <small class="text-muted">{{ $log->actor_role }}</small></td>
                                            <td>{{ $log->reason ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

<script>
    function submitReceivingForm(button, action) {
        if (!confirm('You cannot edit after submitting. Continue?')) {
            return;
        }
        const form = button.closest('form');
        if (!form) {
            return;
        }
        form.action = action;
        let method = form.querySelector('input[name="_method"]');
        if (!method) {
            method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            form.appendChild(method);
        }
        method.value = 'POST';
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const meatToggle = document.getElementById('field-prod_is_meat_rm');
        const syncMeatFields = function () {
            if (!meatToggle) {
                return;
            }
            document.querySelectorAll('[data-visible-if-field="prod_is_meat_rm"]').forEach(function (element) {
                const shouldShow = meatToggle.checked;
                element.classList.toggle('d-none', !shouldShow);
                element.querySelectorAll('input, select, textarea').forEach(function (input) {
                    input.disabled = meatToggle.disabled || !shouldShow;
                });
            });
        };
        if (meatToggle) {
            meatToggle.addEventListener('change', syncMeatFields);
            syncMeatFields();
        }

        const noDefects = document.getElementById('field-qc_no_defects');
        const defectRows = document.getElementById('defect-rows');
        const syncDefects = function () {
            if (!noDefects || !defectRows) {
                return;
            }
            const hidden = noDefects.checked;
            defectRows.classList.toggle('d-none', hidden);
            defectRows.querySelectorAll('input').forEach(function (input) {
                input.disabled = noDefects.disabled || hidden;
            });
        };
        if (noDefects) {
            noDefects.addEventListener('change', syncDefects);
            syncDefects();
        }

        const defectBody = document.getElementById('defect-rows-body');
        const template = document.getElementById('defect-row-template');
        const addButton = document.getElementById('add-defect-row');
        let defectIndex = Date.now();

        if (addButton && defectBody && template) {
            addButton.addEventListener('click', function () {
                const html = template.innerHTML.replace(/__INDEX__/g, defectIndex++);
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = html.trim();
                defectBody.appendChild(wrapper.firstElementChild);
            });
        }

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-defect-row');
            if (!button) {
                return;
            }
            const row = button.closest('tr');
            if (row) {
                row.remove();
            }
        });
    });
</script>
@endsection
