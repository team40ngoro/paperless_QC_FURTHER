
@extends('layouts.app')

@section('title', 'Detail Verif CIP')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Verif CIP</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verif-cip.index') }}">Verif CIP</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Data Verif CIP
                                    @if($item->tanggal)
                                        - {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                    @endif
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('verif-cip.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <a href="{{ route('verif-cip.export-pdf', $item->uuid) }}" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
@if(auth()->user()->hasPermissionTo('edit-verif-cip'))
                                    <a href="{{ route('verif-cip.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
</div>
                            </div>
                            <div class="card-body">
                                @php
                                    $forms = $item->payload['forms'] ?? [];
                                    $stepLabels = [
                                        'ro1' => 'Rinse Outside 1',
                                        'ri1' => 'Rinse Inside 1',
                                        'ro2' => 'Rinse Outside 2',
                                        'ri2' => 'Rinse Inside 2',
                                        'hc' => 'Hot Clean Inside',
                                        'hci' => 'Hot Clean Outside',
                                        'ro3' => 'Rinse Outside 3',
                                        'ri3' => 'Rinse Inside 3',
                                        'dis' => 'Disinfection',
                                        'diso' => 'Disinfection Out',
                                        'ro4' => 'Rinse Outside 4',
                                        'ri4' => 'Rinse Inside 4',
                                    ];
                                    $stepOrder = array_keys($stepLabels);
                                @endphp

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Plan</label>
                                            <div>{{ $item->plan->nama_plan ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Dibuat Oleh</label>
                                            <div>{{ $item->user->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Dibuat Pada</label>
                                            <div>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                @if(!is_array($forms) || count($forms) === 0)
                                    <div class="alert alert-warning mb-0">Payload kosong.</div>
                                @else
                                    @foreach($forms as $formIndex => $form)
                                        @php
                                            $formTanggal = data_get($form, 'tanggal');
                                            $details = data_get($form, 'details', []);
                                        @endphp

                                        <div class="card card-outline card-primary mb-3">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    Form #{{ $formIndex + 1 }}
                                                    @if($formTanggal)
                                                        - {{ \Carbon\Carbon::parse($formTanggal)->format('d/m/Y') }}
                                                    @endif
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if(!is_array($details) || count($details) === 0)
                                                    <div class="text-muted">Tidak ada detail.</div>
                                                @else
                                                    @foreach($details as $detailIndex => $detail)
                                                        @php
                                                            $steps = data_get($detail, 'steps', []);
                                                        @endphp

                                                        <div class="card card-outline card-info mb-3">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Detail #{{ $detailIndex + 1 }}</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Jenis Mouldrum</label>
                                                                            <div>{{ data_get($detail, 'jenis_mouldrum', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>pH Air Bilasan Terakhir</label>
                                                                            <div>{{ data_get($detail, 'ph_air', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Pressure</label>
                                                                            <div>{{ data_get($detail, 'pressure', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Kondisi Sebelum</label>
                                                                            <div>{{ data_get($detail, 'kondisi_sebelum', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Kondisi Sesudah</label>
                                                                            <div>{{ data_get($detail, 'kondisi_sesudah', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Keterangan</label>
                                                                            <div>{{ data_get($detail, 'keterangan', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Tindakan Koreksi</label>
                                                                            <div>{{ data_get($detail, 'tindakan_koreksi', '-') ?: '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-2 mb-2"><strong>Step Proses Cleaning</strong></div>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered table-sm">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th>Step</th>
                                                                                <th>Suhu Aktual (°C)</th>
                                                                                <th>Suhu Display (°C)</th>
                                                                                <th>Waktu (dtk)</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @if(is_array($steps) && count($steps))
                                                                                @foreach($stepOrder as $stepKey)
                                                                                    @if(array_key_exists($stepKey, $steps))
                                                                                        @php
                                                                                            $step = $steps[$stepKey];
                                                                                        @endphp
                                                                                        <tr>
                                                                                            <td>{{ $stepLabels[$stepKey] ?? $stepKey }}</td>
                                                                                            <td>{{ data_get($step, 'suhu', '-') ?: '-' }}</td>
                                                                                            <td>{{ data_get($step, 'suhu_display', '-') ?: '-' }}</td>
                                                                                            <td>
                                                                                                {{ data_get($step, 'waktu', '-') ?: '-' }}
                                                                                                @if(data_get($step, 'std_waktu'))
                                                                                                    <small class="text-muted d-block">Std: {{ data_get($step, 'std_waktu') }}</small>
                                                                                                @endif
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endif
                                                                                @endforeach

                                                                                @foreach($steps as $stepKey => $step)
                                                                                    @if(!in_array($stepKey, $stepOrder, true))
                                                                                        <tr>
                                                                                            <td>{{ $stepLabels[$stepKey] ?? $stepKey }}</td>
                                                                                            <td>{{ data_get($step, 'suhu', '-') ?: '-' }}</td>
                                                                                            <td>{{ data_get($step, 'suhu_display', '-') ?: '-' }}</td>
                                                                                            <td>
                                                                                                {{ data_get($step, 'waktu', '-') ?: '-' }}
                                                                                                @if(data_get($step, 'std_waktu'))
                                                                                                    <small class="text-muted d-block">Std: {{ data_get($step, 'std_waktu') }}</small>
                                                                                                @endif
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endif
                                                                                @endforeach
                                                                            @else
                                                                                <tr>
                                                                                    <td colspan="4" class="text-center">Tidak ada step.</td>
                                                                                </tr>
                                                                            @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

