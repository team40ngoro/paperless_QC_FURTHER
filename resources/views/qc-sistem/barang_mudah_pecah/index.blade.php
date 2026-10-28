@extends('layouts.app')

@section('title', 'Data Barang Mudah Pecah')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-box text-info"></i> Data Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Barang Mudah Pecah</li>
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
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <!-- Baris toolbar utama: judul kiri, aksi kanan -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0">
                                    <i class="fas fa-list text-info mr-2"></i> Data Barang Mudah Pecah
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('barang-mudah-pecah.download-template') }}" class="dropdown-item">
                                                <i class="fas fa-download text-success mr-2"></i> Download Template
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
                                                <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                            </a>
                                        </div>
                                    </div> -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                                <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                            </a>
                                        </div>
                                    </div>
                                    @if(auth()->user()->hasPermissionTo('create-barang-mudah-pecah'))
                                        <a href="{{ route('barang-mudah-pecah.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Riwayat Audit">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('barang-mudah-pecah.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Bar aksi bulk approve: hanya muncul saat ada baris yang dicentang -->
                            <div class="approval-buttons-group d-none">
                                @php
                                    $userRole = auth()->user()->id_role;
                                    $canApproveQc = in_array($userRole, [1, 3, 5]);
                                    $canApproveProduksi = in_array($userRole, [1, 2, 5]);
                                    $canApproveSPV = in_array($userRole, [1, 4, 5]);
                                @endphp

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
                                    <button type="button" class="btn btn-sm btn-warning shadow-sm approve-selected-btn" data-type="spv" style="color: white;">
                                        <i class="fas fa-check mr-1"></i> Approve SPV
                                    </button>
                                @endif
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
                            
                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('barang-mudah-pecah.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('barang-mudah-pecah.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Barang" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('barang-mudah-pecah.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Nama Barang</th>
                                            <th class="py-3 text-center">Jumlah</th>
                                            <th class="py-3 text-center">Kondisi</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $no = 1;
                                        @endphp

                                        @forelse($items as $item)
                                            <tr class="item-row">
                                                <td class="text-center align-middle">
                                                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}">
                                                </td>
                                                <td class="text-center align-middle">{{ $no }}</td>
                                                <td class="text-center align-middle">{{ $item->shift->shift ?? '-' }}</td>
                                                <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') : '-' }}</td>
                                                <td class="text-center align-middle">{{ $item->jam ?? '-' }}</td>
                                                <td class="text-left align-middle">
                                                    @if($item->is_manual)
                                                        <span class="badge badge-secondary mr-1">Manual</span>{{ $item->nama_barang_manual ?? '-' }}
                                                    @else
                                                        {{ $item->namaBarang->nama_barang ?? '-' }}
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">{{ $item->jumlah ?? '-' }}</td>
                                                <td class="text-center align-middle">
                                                    <span class="badge {{ $item->kondisi === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->kondisi ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div style="font-size: 0.8rem;">{{ $item->user->name ?? '-' }}</div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex flex-column align-items-center" style="gap: 2px;">
                                                        @if($item->approved_by_qc)
                                                            <span class="badge badge-success w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> QC</span>
                                                        @endif
                                                        @if($item->approved_by_produksi)
                                                            <span class="badge badge-primary w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> PRD</span>
                                                        @endif
                                                        @if($item->approved_by_spv)
                                                            <span class="badge badge-dark w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> SPV</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle" style="white-space: nowrap;">
                                                    <x-action-buttons :item="$item" class="tombol" route-prefix="barang-mudah-pecah" />
                                                    <div class="mt-2 d-flex justify-content-center">
                                                        <x-audit-dropdown :item="$item" route-prefix="barang-mudah-pecah" />
                                                    </div>
                                                </td>
                                            </tr>
                                            @php
                                                $no++;
                                            @endphp
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-5 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>Tidak ada data</p>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('barang-mudah-pecah.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Barang Mudah Pecah</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info alert-dismissible border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Penting!</strong> Gunakan template yang telah disediakan untuk memastikan format data benar.
                        <a href="{{ route('barang-mudah-pecah.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Pilih File Excel</label>
                        <div class="custom-file shadow-sm">
                            <input type="file" class="custom-file-input" id="excelFile" name="file" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="excelFile">Pilih file...</label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-file-excel text-success mr-1"></i>
                            Format: .xlsx atau .xls | Ukuran maksimal: 10MB
                        </small>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Preview:</label>
                        <div id="fileInfo" class="alert alert-light border">
                            <small class="text-muted">Belum ada file dipilih</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-upload mr-2"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('barang-mudah-pecah.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
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
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <select name="shift_id" class="form-control">
                                <option value="">Semua Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
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
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 10/00" value="QF 10/00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('barang-mudah-pecah.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .group-header {
        background-color: #f1f5f9 !important;
        border-left: 5px solid #17a2b8 !important;
    }
    .group-header td {
        padding: 1rem !important;
    }
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
    }
    .bulk-approve-btn {
        transition: all 0.2s ease;
    }
    .bulk-approve-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
    .approval-buttons-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding: 0.6rem 0.9rem;
        background-color: #eef6fc;
        border: 1px solid #cfe6f5;
        border-radius: 6px;
    }
    .tombol {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
    }
    .tombol i {
        font-size: 0.85rem !important;
    }
</style>

@push('scripts')
<script>
// Handle file selection preview
$('#excelFile').on('change', function() {
    const file = this.files[0];
    const label = $(this).next('.custom-file-label');
    const info = $('#fileInfo');
    
    if (file) {
        label.text(file.name);
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        info.html(`
            <div>
                <i class="fas fa-check-circle text-success mr-2"></i>
                <strong>${file.name}</strong><br>
                <small class="text-muted">Ukuran: ${sizeMB}MB | Tipe: ${file.type}</small>
            </div>
        `);
    } else {
        label.text('Pilih file...');
        info.html('<small class="text-muted">Belum ada file dipilih</small>');
    }
});

$(document).ready(function() {
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

    // Handle individual checkbox click
    $(document).on('change', '.row-checkbox', function() {
        const uuid = $(this).data('uuid');
        
        if ($(this).is(':checked')) {
            if (!selectedUuids.includes(uuid)) {
                selectedUuids.push(uuid);
            }
        } else {
            selectedUuids = selectedUuids.filter(id => id !== uuid);
        }
        
        updateSelectedCount();
    });

    // Handle select all checkbox
    $('#selectAllCheckbox').on('change', function() {
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
            selectedUuids = [];
        }
        
        updateSelectedCount();
    });

    // Bulk Approval Handler for Selected Rows
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

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('barang-mudah-pecah.bulk-approve-selected') }}",
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
                        }).then(() => {
                            selectedUuids = [];
                            location.reload();
                        });
                    } else {
                        alert(response.message);
                        selectedUuids = [];
                        location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Kesalahan sistem';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                } else {
                    alert('Error: ' + errorMsg);
                }
                btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
            }
        });
    });
});
</script>
@endpush

@endsection
