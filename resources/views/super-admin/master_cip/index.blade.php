@extends('layouts.app')

@section('title', 'Master CIP - Standar Waktu')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Master CIP</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item active">Master CIP</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">



        <div class="card card-primary card-outline">
          <div class="card-header">
            <div class="row">
              <div class="col-md-8">
                <form action="{{ route('master-cip.index') }}" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label class="mr-2">Show</label>
                        <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <label class="ml-2">entries</label>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Tahap..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('master-cip.index') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
              </div>
              <div class="col-md-4 text-right">
                <a href="{{ route('master-cip.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
              <table class="table text-center table-bordered table-striped" style="white-space:nowrap;">
                <thead>
                <tr>
                  <th style="width:50px">#</th>
                
                  <th>Nama Tahap</th>
                  <th>Std Waktu (dtk)</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @php $no = $masters->firstItem(); @endphp
                @forelse($masters as $i => $m)
                  <tr>
                    <td>{{ $no++ }}</td>
                   
                    <td>{{ $m->step_label }}</td>
                    <td>{{ $m->std_waktu !== null ? number_format($m->std_waktu, 0) . ' dtk' : '-' }}</td>
                    <td>
                      <a href="{{ route('master-cip.edit', $m->uuid) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                      <form action="{{ route('master-cip.destroy', $m->uuid) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data master CIP.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $masters->appends(['search' => $search, 'per_page' => $perPage])->links('pagination::bootstrap-4') }}
            </div>
          </div>
        </div>

      </div>
    </section>
</div>
@endsection
