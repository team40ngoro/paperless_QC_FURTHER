@extends('layouts.app')
@section('title', 'Master Bahan Baku Rice Bites')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-seedling mr-2 text-primary"></i>Master Bahan Baku Rice Bites</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Bahan Baku Rice Bites</li>
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
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <form action="{{ route('bahan-baku-rice-bites.index') }}" method="GET" class="form-inline flex-wrap gap-2">
                                        {{-- Show entries --}}
                                        <div class="form-group mr-2 mb-1">
                                            <label class="mr-1">Show</label>
                                            <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                                                @foreach([5,10,25,50,100] as $pp)
                                                    <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                                @endforeach
                                            </select>
                                            <label class="ml-1">entries</label>
                                        </div>

                                        {{-- Filter Plan (superadmin only) --}}
                                        @if(auth()->user()->role === 'superadmin')
                                        <div class="form-group mr-2 mb-1">
                                            <select name="id_plan" class="form-control form-control-sm" onchange="this.form.submit()">
                                                <option value="">-- Semua Plan --</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" {{ $filterPlan == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->nama_plan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif

                                        {{-- Search --}}
                                        <div class="input-group input-group-sm mb-1">
                                            <input type="text" name="search" class="form-control"
                                                   placeholder="Cari nama bahan / produk..."
                                                   value="{{ $search }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if($search)
                                                    <a href="{{ route('bahan-baku-rice-bites.index') }}" class="btn btn-default">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a href="{{ route('bahan-baku-rice-bites.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Tambah Bahan Baku
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            {{-- Alert --}}
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                                    <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover text-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="50">No</th>
                                            @if(auth()->user()->role === 'superadmin')
                                                <th>Plan</th>
                                            @endif
                                            <th>Nama Produk</th>
                                            <th>Nomor Formula</th>
                                            <th>Nama Bahan Baku</th>
                                            <th width="100">Berat (kg)</th>
                                            <!-- <th>Dibuat Oleh</th> -->
                                            <th width="120">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = $paginated_data->firstItem(); @endphp
                                        @forelse($grouped_data as $planId => $produkGroups)
                                            @foreach($produkGroups as $produkId => $formulaGroups)
                                                @php 
                                                    $isFirstProdukRow = true; 
                                                    $produkKey = $planId . '_' . $produkId;
                                                @endphp
                                                @foreach($formulaGroups as $formula => $items)
                                                    @php 
                                                        $isFirstFormulaRow = true; 
                                                        $formulaKey = $produkKey . '_' . $formula;
                                                    @endphp
                                                    @foreach($items as $item)
                                                        <tr>
                                                            @if($isFirstProdukRow)
                                                                <td rowspan="{{ $rowspan_produk[$produkKey] }}">{{ $no++ }}</td>
                                                                @if(auth()->user()->role === 'superadmin')
                                                                    <td rowspan="{{ $rowspan_produk[$produkKey] }}">
                                                                        <span class="badge badge-info">{{ $item->plan->nama_plan ?? '-' }}</span>
                                                                    </td>
                                                                @endif
                                                                <td rowspan="{{ $rowspan_produk[$produkKey] }}" class="text-left font-weight-bold">
                                                                    {{ $item->produk->nama_produk ?? '-' }}
                                                                </td>
                                                                @php $isFirstProdukRow = false; @endphp
                                                            @endif
                                                            
                                                            @if($isFirstFormulaRow)
                                                                <td rowspan="{{ $rowspan_formula[$formulaKey] }}">
                                                                    {{ $item->nomor_formula ?? '-' }}
                                                                </td>
                                                                @php $isFirstFormulaRow = false; @endphp
                                                            @endif
                                                            
                                                            <td class="text-left">
                                                                {{ $item->nama_bahan_baku }}
                                                            </td>
                                                            <td>
                                                                @if($item->berat)
                                                                    <span class="badge badge-secondary">{{ $item->berat }} kg</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('bahan-baku-rice-bites.edit', $item->uuid) }}"
                                                                   class="btn btn-warning btn-sm" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('bahan-baku-rice-bites.destroy', $item->uuid) }}"
                                                                      method="POST" style="display:inline;"
                                                                      onsubmit="return confirm('Yakin hapus data bahan baku {{ $item->nama_bahan_baku }}?')">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->role === 'superadmin' ? 8 : 7 }}" class="text-center py-4">
                                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">Belum ada data bahan baku rice bites.</p>
                                                @if(!$search)
                                                    <a href="{{ route('bahan-baku-rice-bites.create') }}" class="btn btn-success btn-sm mt-2">
                                                        <i class="fas fa-plus mr-1"></i> Tambah Sekarang
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <div class="float-left">
                                <small class="text-muted">
                                    Menampilkan {{ $paginated_data->firstItem() ?? 0 }} - {{ $paginated_data->lastItem() ?? 0 }}
                                    dari {{ $paginated_data->total() }} data
                                </small>
                            </div>
                            <div class="float-right">
                                {{ $paginated_data->appends(['search' => $search, 'per_page' => $perPage, 'id_plan' => $filterPlan])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
