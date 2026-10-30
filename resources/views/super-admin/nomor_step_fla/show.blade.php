@extends('layouts.app')

@section('title', 'Detail Nomor Step Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Nomor Step Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nomor-step-formula-fla.index') }}">Data Nomor Step Formula FLA</a></li>
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
                            <h3 class="card-title">Informasi Nomor Step Formula FLA</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">UUID</th>
                                            <td>: {{ $item->uuid }}</td>
                                        </tr>
                                        <tr>
                                            <th>Plan</th>
                                            <td>: {{ $item->plan->nama_plan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>User</th>
                                            <td>: {{ $item->user->name ?? '-' }}</td>
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
                                        <tr>
                                            <th width="30%">Nomor Step</th>
                                            <td>: {{ $item->nomor_step }}</td>
                                        </tr>
                                        <tr>
                                            <th>Proses</th>
                                            <td>: 
                                                @php
                                                    $prosesArray = explode(',', $item->proses);
                                                @endphp
                                                @foreach($prosesArray as $proses)
                                                    <span class="badge badge-info mr-1">{{ trim($proses) }}</span>
                                                @endforeach
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
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('nomor-step-formula-fla.edit', $item->uuid) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('nomor-step-formula-fla.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <form action="{{ route('nomor-step-formula-fla.destroy', $item->uuid) }}" 
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
