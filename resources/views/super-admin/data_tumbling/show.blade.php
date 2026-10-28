@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Data Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('data-tumbling.index') }}">Data Tumbling</a></li>
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
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-eye"></i> Detail Data Tumbling
                                </h3>
                            </div>

                            <div class="card-body">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-info-circle"></i> Informasi Produk
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Plan:</strong> {{ $dataTumbling->plan->nama_plan ?? '-' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Produk:</strong> {{ $dataTumbling->produk->nama_produk ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-cogs"></i> Parameter Tumbling (Vakum)
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6"><strong>Drum On:</strong> {{ $dataTumbling->drum_on ?? '-' }}</div>
                                                    <div class="col-md-6"><strong>Drum Off:</strong> {{ $dataTumbling->drum_off ?? '-' }}</div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6"><strong>Drum Speed:</strong> {{ $dataTumbling->drum_speed ?? '-' }}</div>
                                                    <div class="col-md-6"><strong>Total Waktu:</strong> {{ $dataTumbling->total_waktu ?? '-' }}</div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12"><strong>Tekanan Vakum:</strong> {{ $dataTumbling->tekanan_vakum ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-cogs"></i> Parameter Tumbling (Non Vakum)
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6"><strong>Drum On:</strong> {{ $dataTumbling->drum_on_non_vakum ?? '-' }}</div>
                                                    <div class="col-md-6"><strong>Drum Off:</strong> {{ $dataTumbling->drum_off_non_vakum ?? '-' }}</div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6"><strong>Drum Speed:</strong> {{ $dataTumbling->drum_speed_non_vakum ?? '-' }}</div>
                                                    <div class="col-md-6"><strong>Total Waktu:</strong> {{ $dataTumbling->total_waktu_non_vakum ?? '-' }}</div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12"><strong>Tekanan Non Vakum:</strong> {{ $dataTumbling->tekanan_non_vakum ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('data-tumbling.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <a href="{{ route('data-tumbling.edit', ['uuid' => $dataTumbling->uuid]) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
