{{-- filepath: resources/views/super-admin/bahan_forming/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Bahan Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Bahan Forming</li>
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
                                <div class="col-md-8">
                                    <form action="{{ route('bahan-forming.index') }}" method="GET" class="form-inline">
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
                                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk..." value="{{ $search }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if($search)
                                                    <a href="{{ route('bahan-forming.index') }}" class="btn btn-default">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-right">
                                    <!-- <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#tarikDataModal">
                                      <i class="fas fa-file-download"></i> Tarik Data
                                    </button> -->
                                    <button type="button" class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#importExcelModal">
                                      <i class="fas fa-file-excel"></i> Import Excel
                                    </button>
                                    <a href="{{ route('bahan-forming.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Bahan Forming
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Tarik Data Excel -->
                        <div class="modal fade" id="tarikDataModal" tabindex="-1" role="dialog" aria-labelledby="tarikDataModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('bahan-forming.export-excel') }}" method="GET" class="w-100">
                              <div class="modal-content shadow-lg border-0 rounded-3">
                                <div class="modal-header bg-info text-white rounded-top">
                                  <h5 class="modal-title" id="tarikDataModalLabel">
                                    <i class="fas fa-file-download"></i> Tarik Data Bahan Forming
                                  </h5>
                                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body text-left">
                                  <div class="alert alert-info mb-3">
                                    <strong>Petunjuk:</strong> Pilih produk terlebih dahulu untuk menarik data Bahan Forming. Format file Excel yang dihasilkan sama persis dengan template import Excel.
                                  </div>
                                  <div class="form-group">
                                    <label for="id_produk" class="font-weight-bold">Pilih Produk <span class="text-danger">*</span></label>
                                    <select name="id_produk" id="id_produk" class="form-control" required>
                                      <option value="all">-- Semua Produk --</option>
                                      @foreach($produks as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                                      @endforeach
                                    </select>
                                  </div>
                                </div>
                                <div class="modal-footer bg-light rounded-bottom">
                                  <button type="submit" class="btn btn-info">
                                    <i class="fas fa-download"></i> Tarik Data
                                  </button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Batal
                                  </button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        {{-- End Modal Tarik Data --}}

                        <!-- Modal Import Excel -->
                        <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('bahan-forming.import-excel') }}" method="POST" enctype="multipart/form-data" class="w-100">
                              @csrf
                              <div class="modal-content shadow-lg border-0 rounded-3">
                                <div class="modal-header bg-success text-white rounded-top">
                                  <h5 class="modal-title" id="importExcelModalLabel">
                                    <i class="fas fa-file-excel"></i> Import Jenis Produk dari Excel
                                  </h5>
                                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <div class="alert alert-info mb-3">
                                    <strong>Petunjuk:</strong> Download template Excel terlebih dahulu, isi data sesuai format, lalu upload kembali file tersebut untuk import data produk.
                                  </div>
                                  <div class="d-flex justify-content-center mb-3">
                                    <a href="{{ route('bahan-forming.download-template') }}" class="btn btn-outline-primary">
                                      <i class="fas fa-download"></i> Download Template
                                    </a>
                                  </div>
                                  <div class="form-group">
                                    <label for="file" class="font-weight-bold">Pilih File Excel (.xlsx)</label>
                                    <input type="file" name="file" class="form-control-file" accept=".xlsx,.xls" required>
                                  </div>
                                </div>
                                <div class="modal-footer bg-light rounded-bottom">
                                  <button type="submit" class="btn btn-success">
                                    <i class="fas fa-upload"></i> Import
                                  </button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Batal
                                  </button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        {{-- End Modal --}}
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
                                <table class="table text-center table-bordered table-striped w-100">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Nomor Formula</th>
                                        <th>Nama RM</th>
                                        <th>Berat RM (kg)</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = $paginated_formulas->firstItem(); @endphp
                                    @foreach($grouped_data as $produkId => $formulaGroups)
                                        @php $isFirstProdukRow = true; @endphp
                                        @foreach($formulaGroups as $formulaId => $items)
                                            @php $isFirstFormulaRow = true; @endphp
                                            @foreach($items as $item)
                                                <tr>
                                                    @if($isFirstProdukRow)
                                                        <td rowspan="{{ $rowspan_produk[$produkId] ?? 1 }}">{{ $no++ }}</td>
                                                        <td rowspan="{{ $rowspan_produk[$produkId] ?? 1 }}">{{ $item->produk->nama_produk ?? '-' }}</td>
                                                        @php $isFirstProdukRow = false; @endphp
                                                    @endif
                                                    @if($isFirstFormulaRow)
                                                        <td rowspan="{{ $rowspan_formula[$produkId . '_' . $formulaId] ?? 1 }}">{{ $item->formula->nomor_formula ?? '-' }}</td>
                                                        @php $isFirstFormulaRow = false; @endphp
                                                    @endif
                                                    <td><span style="text-transform:uppercase;">{{ $item->nama_rm }}</span></td>
                                                    <td>{{ $item->berat_rm }} kg</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('bahan-forming.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('bahan-forming.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')" {{ $item->referensi_count > 0 ? 'disabled' : '' }}>
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{ $paginated_formulas->appends(['search' => $search, 'per_page' => $perPage])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection