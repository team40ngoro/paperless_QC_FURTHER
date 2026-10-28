{{-- filepath: resources/views/super-admin/nomor-formula/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Nomor Formula</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Nomor Formula</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                                                <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama Produk...">
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('nomor-formula.create') }}" class="btn btn-primary btn-sm float-right">
                                        <i class="fas fa-plus"></i> Tambah Nomor Formula
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="productContainer">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @php
                                // Kelompokkan berdasarkan nama produk
                                $grouped = $nomor_formula->groupBy(function($item) {
                                    return $item->produk->nama_produk ?? 'Tanpa Produk';
                                });
                            @endphp

                            @foreach($grouped as $produkName => $formulas)
                                <div class="card card-outline card-info mb-4 product-card" data-name="{{ strtolower($produkName) }}">
                                    <div class="card-header">
                                        <h3 class="card-title font-weight-bold"><i class="fas fa-box mr-2"></i>{{ $produkName }}</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomor Formula</th>
                                                    <th>Nama Plan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($formulas as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><span class="badge badge-primary">{{ $item->nomor_formula }}</span></td>
                                                    <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('nomor-formula.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($item->bahanForming()->exists())
                                                            <button class="btn btn-danger btn-sm" disabled title="Ada {{ $item->bahanForming()->count() }} data terkait">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @else
                                                            <form action="{{ route('nomor-formula.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">
                                                                    <i class="fas fa-trash"></i> 
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <script>
                        document.getElementById('searchInput').addEventListener('keyup', function() {
                            let filter = this.value.toLowerCase();
                            let cards = document.querySelectorAll('.product-card');
                            cards.forEach(card => {
                                let name = card.getAttribute('data-name');
                                card.style.display = name.includes(filter) ? "" : "none";
                            });
                        });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection