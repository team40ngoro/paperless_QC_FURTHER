@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-flask text-primary"></i> Verifikasi Pembuatan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Verifikasi Pembuatan Emulsi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0">
                                    <i class="fas fa-list text-primary mr-2"></i> Data Persiapan Bahan Emulsi
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    {{-- Group Tombol Approval Massal Berdasarkan Checkbox --}}
                                    @php $userRole = auth()->user()->id_role ?? null; @endphp
                                    <div id="approvalButtonsGroup" class="btn-group shadow-sm mr-2 approval-buttons-group" style="display: none;">
                                        <span class="btn btn-sm btn-light border disabled font-weight-bold text-dark d-flex align-items-center">
                                            <i class="fas fa-check-double mr-1 text-primary"></i>
                                            <span id="selectedCount">0</span>&nbsp;Terpilih:
                                        </span>
                                        @if(in_array($userRole, [1, 3, 5]))
                                            <button type="button" class="btn btn-sm btn-success bulk-approve-selected-btn" data-type="qc" style="font-weight: 600;">
                                                <i class="fas fa-check mr-1"></i> Appr QC
                                            </button>
                                        @endif
                                        @if(in_array($userRole, [1, 2, 5]))
                                            <button type="button" class="btn btn-sm btn-primary bulk-approve-selected-btn" data-type="produksi" style="font-weight: 600;">
                                                <i class="fas fa-check mr-1"></i> Appr PROD
                                            </button>
                                        @endif
                                        @if(in_array($userRole, [1, 4, 5]))
                                            <button type="button" class="btn btn-sm btn-dark bulk-approve-selected-btn" data-type="spv" style="font-weight: 600;">
                                                <i class="fas fa-check mr-1"></i> Appr SPV
                                            </button>
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                    </button>
                                    <a href="{{ route('persiapan-bahan-emulsi.audit') }}" class="btn btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Lihat Data Audit">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-emulsi'))
                                        <a href="{{ route('persiapan-bahan-emulsi.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-exclamation-circle"></i> {{ session('error') }}
                                </div>
                            @endif

                            <!-- Form Pencarian -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('persiapan-bahan-emulsi.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('persiapan-bahan-emulsi.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk / Kode" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('persiapan-bahan-emulsi.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center align-middle py-3" style="width: 40px;">
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih Semua di Halaman Ini">
                                                </div>
                                            </th>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-center">Kode Produksi</th>
                                            <th class="py-3 text-left">Produk</th>
                                            <th class="py-3 text-center">Emulsi</th>
                                            <th class="py-3 text-center">Proses</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @include('qc-sistem.persiapan_bahan_emulsi._table')
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4" id="paginationLinks">
                                {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('persiapan-bahan-emulsi.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-0">
                            <label class="font-weight-bold small text-uppercase text-muted">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group col-md-6 mb-0">
                            <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <select name="shift_id" class="form-control">
                                <option value="">Semua Shift</option>
                                @php
                                    $shifts = auth()->user()->role === 'superadmin'
                                        ? \App\Models\DataShift::orderBy('shift')->get()
                                        : \App\Models\DataShift::where('id_plan', auth()->user()->id_plan)->orderBy('shift')->get();
                                @endphp
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Produk</label>
                        <div class="input-group">
                            @php
                                $pdfProduksEmulsi = auth()->user()->role === 'superadmin'
                                    ? \App\Models\JenisProduk::orderBy('nama_produk')->get()
                                    : \App\Models\JenisProduk::where('id_plan', auth()->user()->id_plan)->orderBy('nama_produk')->get();
                            @endphp
                            <select name="id_produk" id="select2-produk-emulsi" class="form-control">
                                <option value="">Semua Produk</option>
                                @foreach($pdfProduksEmulsi as $produk)
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
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: F-PRO-01-01" value="QF 05/00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" formaction="{{ route('persiapan-bahan-emulsi.bulk-export-excel') }}"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" formaction="{{ route('persiapan-bahan-emulsi.bulk-export-pdf') }}"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
    }
    .status-badges .badge {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 3px;
        padding: 5px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    #mainTable thead th {
        border-bottom: 2px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        vertical-align: middle !important;
    }
    .item-row td {
        vertical-align: middle !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.85rem;
    }
    .row-checkbox, #selectAllCheckbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        margin: 0 !important;
        position: relative !important;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // State management checkbox terpolasi
    let selectedUuids = new Set();

    function updateApprovalButtons() {
        const count = selectedUuids.size;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#approvalButtonsGroup').fadeIn(200);
        } else {
            $('#approvalButtonsGroup').fadeOut(200);
        }
    }

    function syncCheckboxesOnCurrentPage() {
        let allChecked = true;
        let hasCheckboxes = false;

        $('.row-checkbox').each(function() {
            hasCheckboxes = true;
            const uuid = $(this).data('uuid');
            if (selectedUuids.has(uuid)) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
                allChecked = false;
            }
        });

        $('#selectAllCheckbox').prop('checked', hasCheckboxes && allChecked);
    }

    // Toggle Checkbox Utama (Select All)
    $(document).on('change', '#selectAllCheckbox', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').each(function() {
            const uuid = $(this).data('uuid');
            $(this).prop('checked', isChecked);
            if (isChecked) {
                selectedUuids.add(uuid);
            } else {
                selectedUuids.delete(uuid);
            }
        });
        updateApprovalButtons();
    });

    // Toggle Checkbox Per-Baris
    $(document).on('change', '.row-checkbox', function() {
        const uuid = $(this).data('uuid');
        if ($(this).is(':checked')) {
            selectedUuids.add(uuid);
        } else {
            selectedUuids.delete(uuid);
        }
        
        let allChecked = true;
        $('.row-checkbox').each(function() {
            if (!$(this).is(':checked')) {
                allChecked = false;
            }
        });
        $('#selectAllCheckbox').prop('checked', allChecked);

        updateApprovalButtons();
    });

    // Handler Tombol Bulk Approve Terpilih
    $(document).on('click', '.bulk-approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');
        const uuidsArray = Array.from(selectedUuids);

        if (uuidsArray.length === 0) {
            alert('Pilih minimal satu data untuk disetujui.');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menyetujui ${uuidsArray.length} data yang dipilih sebagai ${type.toUpperCase()}?`)) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        $.ajax({
            url: "{{ route('persiapan-bahan-emulsi.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: uuidsArray
            },
            success: function(response) {
                btn.prop('disabled', false).html(`<i class="fas fa-check mr-1"></i> Appr ${type.toUpperCase()}`);
                if (response.success) {
                    selectedUuids.clear();
                    updateApprovalButtons();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => performSearch(currentPage));
                    } else {
                        alert(response.message);
                        performSearch(currentPage);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal!', response.message, 'error');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(`<i class="fas fa-check mr-1"></i> Appr ${type.toUpperCase()}`);
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', msg, 'error');
                } else {
                    alert('Error: ' + msg);
                }
            }
        });
    });

    // Init Select2 untuk produk di modal PDF emulsi
    $('#select2-produk-emulsi').select2({
        dropdownParent: $('#exportPdfModal'),
        placeholder: 'Semua Produk',
        allowClear: true,
        width: '100%'
    });

    // Form-based GET search
});
</script>
@endpush
@endsection