@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-industry text-primary"></i> Verifikasi Persiapan Cold Mixing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Verifikasi Persiapan Cold Mixing</li>
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
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
                                <h3 class="card-title text-bold mb-0"><i class="fas fa-list-alt text-primary mr-2"></i> Data Verifikasi Persiapan Cold Mixing</h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap:0.5rem;">
                                    <button type="button" class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                    </button>
                                    <a href="{{ route('persiapan-bahan-forming.audit') }}" class="btn btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-forming'))
                                        <a href="{{ route('persiapan-bahan-forming.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Approval Buttons Group (hidden by default, shown when checkbox selected) --}}
                            @php
                                $userRoleAppr = auth()->user()->id_role;
                                $canApproveQc = in_array($userRoleAppr, [1, 3, 5]);
                                $canApproveProduksi = in_array($userRoleAppr, [1, 2, 5]);
                                $canApproveSPV = in_array($userRoleAppr, [1, 4, 5]);
                            @endphp
                            @if($canApproveQc || $canApproveProduksi || $canApproveSPV)
                            <div class="approval-buttons-group d-none" style="display:flex;align-items:center;flex-wrap:wrap;gap:0.5rem;margin-top:0.75rem;padding:0.6rem 0.9rem;background-color:#eef6fc;border:1px solid #cfe6f5;border-radius:6px;">
                                <span class="badge badge-info"><span class="selected-count">0</span> dipilih</span>
                                @if($canApproveQc)
                                    <button type="button" class="btn btn-sm btn-success shadow-sm approve-selected-btn" data-type="qc">
                                        <i class="fas fa-check mr-1"></i> Approve QC
                                    </button>
                                @endif
                                @if($canApproveProduksi)
                                    <button type="button" class="btn btn-sm btn-info shadow-sm approve-selected-btn" data-type="produksi">
                                        <i class="fas fa-check mr-1"></i> Approve Produksi
                                    </button>
                                @endif
                                @if($canApproveSPV)
                                    <button type="button" class="btn btn-sm btn-warning shadow-sm approve-selected-btn" data-type="spv" style="color:white;">
                                        <i class="fas fa-check mr-1"></i> Approve SPV
                                    </button>
                                @endif
                            </div>
                            @endif
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
                                        <form action="{{ route('persiapan-bahan-forming.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('persiapan-bahan-forming.index') }}" method="GET">
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
                                                    <a href="{{ route('persiapan-bahan-forming.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                            <th class="text-center py-3" style="width: 40px;">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih semua">
                                                </div>
                                            </th>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Jenis</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Produk</th>
                                            <th class="py-3 text-center">Kode Produksi</th>
                                            <th class="py-3 text-center">Formula</th>
                                            <th class="py-3 text-center">Kondisi</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3">Catatan</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @include('qc-sistem.persiapan_bahan_forming._table')
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4">
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
            <form action="{{ route('persiapan-bahan-forming.bulk-export-pdf') }}" method="POST" target="_blank">
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
                                $pdfProduksForming = auth()->user()->role === 'superadmin'
                                    ? \App\Models\JenisProduk::orderBy('nama_produk')->get()
                                    : \App\Models\JenisProduk::where('id_plan', auth()->user()->id_plan)->orderBy('nama_produk')->get();
                            @endphp
                            <select name="produk_id" id="select2-produk-forming" class="form-control">
                                <option value="">Semua Produk</option>
                                @foreach($pdfProduksForming as $produk)
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
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: F-PRO-01-01" value="QF 04/00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" formaction="{{ route('persiapan-bahan-forming.bulk-export-excel') }}"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" formaction="{{ route('persiapan-bahan-forming.bulk-export-pdf') }}"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .group-header {
        background-color: #f1f5f9 !important;
        border-left: 5px solid #007bff !important;
    }
    .group-header td {
        padding: 1rem !important;
    }
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
    }
    .btn-approval {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.7rem !important;
        border-radius: 4px;
    }
    .btn-approval:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-approval.active-approval {
        cursor: pointer;
        background-color: #fff;
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
    .row-checkbox,
    #selectAllCheckbox {
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Init Select2 untuk produk di modal PDF forming
    $('#select2-produk-forming').select2({
        dropdownParent: $('#exportPdfModal'),
        placeholder: 'Semua Produk',
        allowClear: true,
        width: '100%'
    });

    // Form-based GET search & per_page navigation

    // =====================================================
    // CHECKBOX-BASED APPROVAL (Per-Row Selection)
    // =====================================================
    let selectedUuids = [];

    function updateSelectedCount() {
        const count = selectedUuids.length;
        $('.selected-count').text(count);
        if (count > 0) {
            $('.approval-buttons-group').removeClass('d-none');
        } else {
            $('.approval-buttons-group').addClass('d-none');
        }
    }

    function updateCheckboxStates() {
        selectedUuids.forEach(function(uuid) {
            $('input.row-checkbox[data-uuid="' + uuid + '"]').prop('checked', true);
        });
        $('#selectAllCheckbox').prop('checked', false);
        updateSelectedCount();
    }

    // Individual checkbox
    $(document).on('change', '.row-checkbox', function() {
        const uuid = $(this).data('uuid');
        if ($(this).is(':checked')) {
            if (!selectedUuids.includes(uuid)) {
                selectedUuids.push(uuid);
            }
        } else {
            selectedUuids = selectedUuids.filter(function(id) { return id !== uuid; });
        }
        updateSelectedCount();
    });

    // Select-All checkbox
    $(document).on('change', '#selectAllCheckbox', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        if (isChecked) {
            $('.row-checkbox').each(function() {
                const uuid = $(this).data('uuid');
                if (!selectedUuids.includes(uuid)) {
                    selectedUuids.push(uuid);
                }
            });
        } else {
            const currentPageUuids = [];
            $('.row-checkbox').each(function() {
                currentPageUuids.push($(this).data('uuid'));
            });
            selectedUuids = selectedUuids.filter(function(id) { return !currentPageUuids.includes(id); });
        }
        updateSelectedCount();
    });

    // Approve Selected button click
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menyetujui ' + selectedUuids.length + ' data sebagai ' + type.toUpperCase() + '?')) {
            return;
        }

        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('persiapan-bahan-forming.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: selectedUuids
            },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        alert(response.message);
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal!', response.message, 'error');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', msg, 'error');
                } else {
                    alert('Error: ' + msg);
                }
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // =====================================================
    // BULK APPROVAL (per group/shift)
    // =====================================================
    $(document).off('click', '.bulk-approve-btn').on('click', '.bulk-approve-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');
        const tanggal = btn.data('tanggal');
        const shift_id = btn.data('shift-id');

        if (!confirm('Apakah Anda yakin ingin menyetujui seluruh data pada shift ini sebagai ' + type.toUpperCase() + '?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('persiapan-bahan-forming.bulk-approve') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                tanggal: tanggal,
                shift_id: shift_id
            },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => performSearch());
                    } else {
                        alert(response.message);
                        performSearch();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal!', response.message, 'error');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                    performSearch();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', msg, 'error');
                } else {
                    alert('Error: ' + msg);
                }
                performSearch();
            }
        });
    });
});
</script>
@endpush
@endsection
