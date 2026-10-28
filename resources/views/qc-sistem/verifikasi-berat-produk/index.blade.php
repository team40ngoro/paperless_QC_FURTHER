@extends('layouts.app')

@section('container')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Verifikasi Berat Produk Per Tahapan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Verifikasi Berat</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">

                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-weight"></i> Data Verifikasi Berat</h3>
                                <div class="card-tools">
                                    <a href="{{ route('wizard.selection') }}" class="btn btn-sm btn-secondary mr-1">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    

                                    <div class="btn-group mr-1">
                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                                <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                            </a>
                                        </div>
                                    </div><!-- /.btn-group -->

                                    <a href="{{ route('verifikasi-berat-produk.audit') }}" class="btn btn-sm btn-purple mr-1">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('create-verifikasi-berat-produk'))
                                        <a href="{{ route('verifikasi-berat-produk.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </a>
                                    @endif
                                </div><!-- /.card-tools -->
                            </div><!-- /.card-header -->

                            <div class="card-body">
                            @include('partials.date-range-filter')

                                <div class="table-responsive">

                                    {{-- Form Pencarian --}}
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                                <form action="{{ route('verifikasi-berat-produk.index') }}" method="GET" class="d-inline">
                                                    @if(request('search'))
                                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                                    @endif
                                                    <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                        <option value="5" {{ ($perPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                                        <option value="10" {{ ($perPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                                        <option value="25" {{ ($perPage ?? 5) == 25 ? 'selected' : '' }}>25</option>
                                                        <option value="50" {{ ($perPage ?? 5) == 50 ? 'selected' : '' }}>50</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-4">
                                            <form method="GET" action="{{ route('verifikasi-berat-produk.index') }}">
                                                @if(request('per_page'))
                                                    <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                                @endif
                                                <div class="input-group shadow-sm">
                                                    <input type="text" class="form-control" name="search"
                                                           placeholder="Cari Produk atau Kode..." value="{{ $search ?? '' }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="submit">
                                                            <i class="fas fa-search mr-1"></i> Cari
                                                        </button>
                                                        @if(!empty($search))
                                                            <a class="btn btn-outline-danger" href="{{ route('verifikasi-berat-produk.index', ['per_page' => request('per_page', 5)]) }}" title="Hapus Pencarian">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </div><!-- /.row (form pencarian) -->

                                    @if($records->count() > 0)
                                        @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'verifikasi-berat-produk.bulk-approve-selected'])
                                    @endif
                                    <table style="white-space: nowrap;" class="table text-center table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="bulkApprovalSelectAll" title="Pilih semua"></th>
                                                <th>No</th>
                                                <th>Rincian Produksi</th>
                                                <th>Status Appr</th>
                                                <th style="width: 150px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = $data->firstItem(); @endphp

                                            @forelse($records as $item)
                                                    <tr>
                                                        <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini"></td>
                                                        <td>{{ $no++ }}</td>
                                                        <td class="text-left">
                                                            <div>
                                                                <span class="badge badge-outline-secondary mr-2">
                                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                                </span>
                                                                <strong>{{ $item->produk->nama_produk ?? '-' }}</strong>
                                                                <span class="mx-2 text-muted">|</span>
                                                                <span class="text-info">{{ $item->kode_produksi }}</span>
                                                                <span class="mx-2 text-muted">|</span>
                                                                <small class="text-muted">Gramase: <b>{{ $item->gramase }}g</b></small>
                                                                <span class="mx-2 text-muted">|</span>
                                                                <small class="badge badge-sm {{ $item->jenis_produk_kfc == 'KFC' ? 'badge-primary' : 'badge-secondary' }}">
                                                                    {{ $item->jenis_produk_kfc == 'KFC' ? 'Produk Yum!' : 'Non-Yum!' }}
                                                                </small>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="status-badges d-flex flex-column align-items-center">
                                                                <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                                                                </span>
                                                                <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                                                                </span>
                                                                <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                                                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <x-action-buttons :item="$item" route-prefix="verifikasi-berat-produk" :show-print="true" />
                                                            <x-audit-dropdown :item="$item" route-prefix="verifikasi-berat-produk" permission-prefix="verifikasi-berat-produk" :uuid="$item->uuid" />
                                                        </td>
                                                    </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div><!-- /.table-responsive -->
                            </div><!-- /.card-body -->

                            <div class="card-footer d-flex justify-content-center">
                                {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div><!-- /.card-footer -->

                        </div><!-- /.card -->
                    </div><!-- /.col-12 -->
                </div><!-- /.row -->

            </div><!-- /.container-fluid -->
        </section><!-- /.content -->

    </div><!-- /.content-wrapper -->

    <style>
        .btn-purple { background-color: #6f42c1; color: white; border-color: #6f42c1; }
        .btn-purple:hover { background-color: #5a32a3; color: white; border-color: #5a32a3; }
    </style>

    {{-- Modal Export PDF --}}
    <div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('verifikasi-berat-produk.bulk-export-pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-file-pdf mr-2"></i>Cetak PDF Verifikasi Berat Produk</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">Tanggal Mulai</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-01') }}">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">Shift</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                </div>
                                <select name="shift_id" class="form-control">
                                    <option value="">Semua Shift</option>
                                    @php
                                        $pdfShifts = auth()->user()->id_role == 1
                                            ? \App\Models\DataShift::orderBy('shift')->get()
                                            : \App\Models\DataShift::where('id_plan', auth()->user()->id_plan)->orderBy('shift')->get();
                                    @endphp
                                    @foreach($pdfShifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">Produk</label>
                            <div class="input-group">
                                <select name="id_produk" class="form-control" id="pdf_produk_filter_verifikasi_berat">
                                    <option value="">Semua Produk</option>
                                    @php
                                        $pdfProduks = auth()->user()->id_role == 1
                                            ? \App\Models\JenisProduk::orderBy('nama_produk')->get()
                                            : \App\Models\JenisProduk::where('id_plan', auth()->user()->id_plan)->orderBy('nama_produk')->get();
                                    @endphp
                                    @foreach($pdfProduks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <input type="text" name="kode_form" class="form-control" value="QF 11/00" required>
                            </div>
                        </div>

                        <input type="hidden" name="is_audit" value="0">
                    </div><!-- /.modal-body -->

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                        <button type="submit" formaction="{{ route('verifikasi-berat-produk.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                        <button type="submit" formaction="{{ route('verifikasi-berat-produk.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    @push('scripts')
        @include('partials.bulk-approval-selected-script')
        <script>
            $(document).ready(function() {
                $('#pdf_produk_filter_verifikasi_berat').select2({
                    dropdownParent: $('#exportPdfModal'),
                    placeholder: 'Pilih Produk',
                    allowClear: true,
                    width: '100%'
                });
            });
        </script>
    @endpush

@endsection