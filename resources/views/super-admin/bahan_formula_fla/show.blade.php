@extends('layouts.app')

@section('title', 'Detail Bahan Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Bahan Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-formula-fla.index') }}">Data Bahan Formula FLA</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Bahan Formula FLA</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                   
                                        <tr>
                                            <th>Plan</th>
                                            <td>: {{ $item->plan->nama_plan ?? '-' }}</td>
                                        </tr>
                                      
                                        <tr>
                                            <th>Nama Produk</th>
                                            <td>: {{ $item->namaFormulaFla->produk->nama_produk ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Formula FLA</th>
                                            <td>: {{ $item->namaFormulaFla->nama_formula_fla ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <!-- <tr>
                                            <th width="30%">Step Formula</th>
                                            <td>: 
                                                <span class="badge badge-info">
                                                    Step {{ $item->nomorStepFormulaFla->nomor_step ?? '-' }}
                                                </span>
                                            </td>
                                        </tr> -->
                                        <tr>
                                            <th>Proses Step</th>
                                            <td>: 
                                                @if($item->nomorStepFormulaFla && $item->nomorStepFormulaFla->proses)
                                                    @php
                                                        $prosesArray = explode(',', $item->nomorStepFormulaFla->proses);
                                                    @endphp
                                                    @foreach($prosesArray as $proses)
                                                        <span class="badge badge-secondary mr-1">{{ trim($proses) }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat</th>
                                            <td>: {{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Diupdate</th>
                                            <td>: {{ $item->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <!-- Bahan Formula FLA -->
                                <div class="col-md-6">
                                    <h5><i class="fas fa-list-ul text-primary"></i> Bahan Formula FLA</h5>
                                    @if($item->getBahanFormulaArray())
                                        <div class="card">
                                            <div class="card-body">
                                                <ol class="mb-0">
                                                    @foreach($item->getBahanFormulaArray() as $bahan)
                                                        <li class="mb-2">
                                                            <span class="badge badge-primary">{{ $bahan }}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Tidak ada data bahan formula FLA
                                        </div>
                                    @endif
                                </div>

                                <!-- Berat Formula FLA -->
                                <div class="col-md-6">
                                    <h5><i class="fas fa-weight text-success"></i> Berat Formula FLA</h5>
                                    @if($item->getBeratFormulaArray())
                                        <div class="card">
                                            <div class="card-body">
                                                <ol class="mb-0">
                                                    @foreach($item->getBeratFormulaArray() as $berat)
                                                        <li class="mb-2">
                                                            <span class="badge badge-success">{{ $berat }} kg</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Tidak ada data berat formula FLA
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('bahan-formula-fla.edit', $item->uuid) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('bahan-formula-fla.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <form action="{{ route('bahan-formula-fla.destroy', $item->uuid) }}" 
                                  method="POST" style="display: inline-block;" class="ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
