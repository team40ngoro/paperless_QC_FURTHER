@extends('layouts.app')

@section('container')
@php
    $canViewProduction = $access->canViewSection(auth()->user(), 'PRODUCTION');
    $canViewQc = $access->canViewSection(auth()->user(), 'QC');
    $columnCount = 5 + ($canViewProduction ? 2 : 0) + ($canViewQc ? 3 : 0);
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-inbox text-info"></i> Incoming Inspection</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Incoming Inspection</li>
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

            <div class="card card-info card-outline">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title text-bold mb-0">
                            <i class="fas fa-list text-info mr-2"></i> Receiving Inspection Records
                        </h3>
                        <div class="d-flex flex-wrap" style="gap: .5rem;">
                            <a href="{{ route('receiving-inspections.export', request()->query()) }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-csv mr-1"></i> Export CSV
                            </a>
                            <a href="{{ route('receiving-inspections.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel mr-1"></i> Export XLSX
                            </a>
                            @if($access->canCreate(auth()->user()))
                                <a href="{{ route('receiving-inspections.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus-circle mr-1"></i> Create Record
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('receiving-inspections.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold">Area</label>
                                <select name="area" class="form-control form-control-sm">
                                    <option value="">All areas</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area }}" {{ ($filters['area'] ?? '') === $area ? 'selected' : '' }}>{{ $registry->areaLabel($area) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold">Date from</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold">Date to</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                            </div>
                            @if($canViewProduction)
                                <div class="col-md-2 mb-2">
                                    <label class="small font-weight-bold">Production status</label>
                                    <select name="prod_status" class="form-control form-control-sm">
                                        <option value="">Any</option>
                                        <option value="DRAFT" {{ ($filters['prod_status'] ?? '') === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                                        <option value="SUBMITTED" {{ ($filters['prod_status'] ?? '') === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
                                    </select>
                                </div>
                            @endif
                            @if($canViewQc)
                                <div class="col-md-2 mb-2">
                                    <label class="small font-weight-bold">QC status</label>
                                    <select name="qc_status" class="form-control form-control-sm">
                                        <option value="">Any</option>
                                        <option value="DRAFT" {{ ($filters['qc_status'] ?? '') === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                                        <option value="SUBMITTED" {{ ($filters['qc_status'] ?? '') === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="small font-weight-bold">QC finding</label>
                                    <select name="has_qc_finding" class="form-control form-control-sm">
                                        <option value="">Any</option>
                                        <option value="1" {{ (string) ($filters['has_qc_finding'] ?? '') === '1' ? 'selected' : '' }}>Has finding</option>
                                        <option value="0" {{ (string) ($filters['has_qc_finding'] ?? '') === '0' ? 'selected' : '' }}>No finding</option>
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-4 mb-2">
                                <label class="small font-weight-bold">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Area, RM name, code, or UUID">
                            </div>
                            <div class="col-md-2 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm mr-1"><i class="fas fa-search"></i> Filter</button>
                                <a href="{{ route('receiving-inspections.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-sm border-bottom">
                            <thead class="bg-light text-uppercase small font-weight-bold">
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Area</th>
                                    @if($canViewQc)<th>QC form</th>@endif
                                    @if($canViewProduction)<th>Production form</th>@endif
                                    @if($canViewProduction)<th>Production</th>@endif
                                    @if($canViewQc)<th>QC</th>@endif
                                    @if($canViewQc)<th>Finding</th>@endif
                                    <th>Created by</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr class="{{ $canViewQc && $record->has_qc_finding ? 'table-warning' : '' }}">
                                        <td>{{ optional($record->inspection_date)->format('Y-m-d') }}</td>
                                        <td>{{ $record->inspection_date ? $registry->weekdayId($record->inspection_date->format('Y-m-d')) : '—' }}</td>
                                        <td><span class="badge badge-info">{{ $registry->areaLabel($record->area) }}</span></td>
                                        @if($canViewQc)<td>{{ $record->qc_form_code }}</td>@endif
                                        @if($canViewProduction)<td>{{ $record->prod_form_code }}</td>@endif
                                        @if($canViewProduction)
                                            <td>
                                                <span class="badge {{ $record->prod_status === 'SUBMITTED' ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $record->prod_status }}
                                                </span>
                                            </td>
                                        @endif
                                        @if($canViewQc)
                                            <td>
                                                <span class="badge {{ $record->qc_status === 'SUBMITTED' ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $record->qc_status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($record->has_qc_finding)
                                                    <span class="badge badge-danger">QC finding</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{ $record->createdBy->name ?? '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('receiving-inspections.show', $record) }}" class="btn btn-xs btn-outline-primary" title="Open">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('receiving-inspections.print', $record) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $columnCount }}" class="text-center text-muted py-4">No inspection records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="small text-muted">
                            Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of {{ $records->total() }}
                        </div>
                        {{ $records->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
