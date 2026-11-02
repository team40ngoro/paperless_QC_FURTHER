@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Audit Trail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('receiving-inspections.index') }}">Incoming Inspection</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('receiving-inspections.show', $record) }}">{{ $registry->areaLabel($record->area) }}</a></li>
                        <li class="breadcrumb-item active">Audit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $registry->areaLabel($record->area) }} · {{ optional($record->inspection_date)->format('Y-m-d') }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('receiving-inspections.show', $record) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to record
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Section</th>
                                    <th>Action</th>
                                    <th>Field</th>
                                    <th>Old value</th>
                                    <th>New value</th>
                                    <th>Actor</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at?->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $log->section }}</td>
                                        <td><span class="badge badge-secondary">{{ $log->action }}</span></td>
                                        <td>{{ $log->field_key ?? '—' }}</td>
                                        <td><code class="small">{{ is_array($log->old_value) ? json_encode($log->old_value) : $log->old_value }}</code></td>
                                        <td><code class="small">{{ is_array($log->new_value) ? json_encode($log->new_value) : $log->new_value }}</code></td>
                                        <td>{{ $log->actor->name ?? $log->actor_user_id ?? '—' }} <small class="text-muted">{{ $log->actor_role }}</small></td>
                                        <td>{{ $log->reason ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">No audit entries yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
