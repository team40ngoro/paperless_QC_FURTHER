@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks text-info"></i> Pemeriksaan Proses Produksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Pemeriksaan Proses Produksi</li>
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
                                    <i class="fas fa-list text-info mr-2"></i> Data Pemeriksaan Proses Produksi
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('pemeriksaan-proses-produksi.download-template') }}" class="dropdown-item">
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
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-proses-produksi'))
                                        <a href="{{ route('pemeriksaan-proses-produksi.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Riwayat Audit">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('pemeriksaan-proses-produksi.create') }}" class="btn btn-primary btn-sm shadow-sm">
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
                                        <form action="{{ route('pemeriksaan-proses-produksi.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('pemeriksaan-proses-produksi.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Uraian Permasalahan" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('pemeriksaan-proses-produksi.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                                <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                                            </th>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Area / Shift</th>
                                            <th class="py-3 text-left">Ketidaksesuaian / Uraian Permasalahan</th>
                                            <th class="py-3 text-left">Disposisi / Koreksi</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php $rowNumber = ($records->currentPage() - 1) * $records->perPage(); @endphp
                                        @forelse($records as $item)
                                            @php
                                                $rowNumber++;
                                                $isVerified = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
                                                $tanggalFormatted = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-';
                                                $jamFormatted = $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-';
                                                $approvalSteps = [
                                                    'QC' => $item->approved_by_qc,
                                                    'PROD' => $item->approved_by_produksi,
                                                    'SPV' => $item->approved_by_spv,
                                                ];
                                            @endphp
                                            <tr @class(['item-row', 'row-verified' => $isVerified]) data-uuid="{{ $item->uuid }}">
                                                <td class="text-center align-middle" style="width: 40px;">
                                                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
                                                </td>
                                                <td class="text-center align-middle">{{ $rowNumber }}</td>
                                                <td class="text-center align-middle">{{ $tanggalFormatted }}</td>
                                                <td class="text-center align-middle">{{ $jamFormatted }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold">{{ $item->area->area ?? '-' }}</div>
                                                    <div class="font-weight-bold">Shift {{ $item->shift->shift ?? '-' }}</div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="small">{{ $item->ketidaksesuaian ?? '-' }}</div>
                                                    <div class="text-muted small">{{ Str::limit($item->uraian_permasalahan, 40) }}</div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="small">{{ $item->disposisi ?? '-' }}</div>
                                                    <div class="text-muted small">{{ Str::limit($item->tindakan_koreksi, 40) }}</div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="small text-muted">{{ $item->user->name ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="status-badges d-flex flex-column align-items-center">
                                                        @foreach($approvalSteps as $label => $isApproved)
                                                            <span @class([
                                                                    'badge',
                                                                    'w-100',
                                                                    'badge-success' => $isApproved,
                                                                    'badge-light border' => !$isApproved,
                                                                    'mb-1' => !$loop->last,
                                                                  ])
                                                                  style="font-size: 0.7rem;">
                                                                <i class="fas {{ $isApproved ? 'fa-check' : 'fa-clock' }} mr-1"></i> {{ $label }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <x-action-buttons
                                                        :item="$item"
                                                        route-prefix="pemeriksaan-proses-produksi"
                                                        :show-view="true"
                                                        :show-print="false"
                                                        layout="horizontal"
                                                        class="tombol"
                                                    />
                                                    <x-audit-dropdown :item="$item" route-prefix="pemeriksaan-proses-produksi"/>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Tidak ada data Pemeriksaan Proses Produksi ditemukan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
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

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">
                    <i class="fas fa-file-pdf"></i> Export PDF Pemeriksaan Proses
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('pemeriksaan-proses-produksi.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="is_audit" value="0">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                            @error('tanggal_awal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                            @error('tanggal_akhir')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Shift (Opsional)</label>
                        <select class="form-control" name="shift_id">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts ?? \App\Models\DataShift::all() as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Area (Opsional)</label>
                        <select class="form-control" name="id_area">
                            <option value="">Semua Area</option>
                            @foreach($cachedAreas ?? \App\Models\InputArea::all() as $area)
                                <option value="{{ $area->id }}">{{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kode_form">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="kode_form"
                               name="kode_form"
                               maxlength="50"
                               value="QF 24/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data Pemeriksaan Proses Produksi sesuai rentang tanggal, shift, dan area yang dipilih.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" formaction="{{ route('pemeriksaan-proses-produksi.bulk-export-excel') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('pemeriksaan-proses-produksi.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Pemeriksaan Proses Produksi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info alert-dismissible border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Penting!</strong> Gunakan template yang telah disediakan untuk memastikan format data benar.
                        <a href="{{ route('pemeriksaan-proses-produksi.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
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

    /* Checkbox (header & baris data) */
    .row-checkbox,
    #selectAllCheckbox {
        position: static;
        display: inline-block;
        vertical-align: middle;
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
        accent-color: #007bff;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let selectedUuids = [];

    // Preview file import Excel
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

    function updateSelectedCount() {
        const count = selectedUuids.length;
        $('.selected-count').text(count);

        if (count > 0) {
            $('.approval-buttons-group').removeClass('d-none');
        } else {
            $('.approval-buttons-group').addClass('d-none');
        }
    }

    // Checkbox per baris
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

    // Checkbox pilih semua
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

    // Bulk approve baris terpilih
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');
        const label = type.charAt(0).toUpperCase() + type.slice(1);

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menyetujui ' + selectedUuids.length + ' data sebagai ' + type.toUpperCase() + '?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('pemeriksaan-proses-produksi.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: selectedUuids
            },
            success: function(response) {
                if (response.success) {
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
                    Swal.fire('Gagal!', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + label);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                Swal.fire('Error!', msg, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + label);
            }
        });
    });
});
</script>
@endpush
@endsection