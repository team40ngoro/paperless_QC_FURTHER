@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">List Pemeriksaan Metal Detector</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title mb-0"><i class="fas fa-search"></i> Data Pemeriksaan Metal Detector</h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <button type="button" class="btn btn-danger btn-sm mr-1" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    <a href="{{ route('input-metal-detector.audit') }}" class="btn btn-sm btn-purple mr-1">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('create-input-metal-detector'))
                                        <a href="{{ route('input-metal-detector.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Approval Buttons Group: Hidden by default, show when selection > 0 -->
                            <div class="approval-buttons-group d-none mt-3">
                                @php
                                    $userRole = auth()->user()->id_role;
                                    $canApproveQc = in_array($userRole, [1, 3, 5]);
                                    $canApproveProduksi = in_array($userRole, [1, 2, 5]);
                                    $canApproveSPV = in_array($userRole, [1, 4, 5]);
                                @endphp

                                <div style="padding: 12px 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                        <span style="color: white; font-weight: 600; display: inline-block; padding: 4px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.85rem;">
                                            <i class="fas fa-check-circle mr-1"></i> <span class="selected-count">0</span> dipilih
                                        </span>

                                        @if($canApproveQc)
                                            <button type="button" class="btn btn-sm approve-selected-btn" data-type="qc" style="background-color: #28a745; border: none; color: white; font-weight: 500; padding: 6px 14px; border-radius: 4px;">
                                                <i class="fas fa-check mr-1"></i> QC
                                            </button>
                                        @endif
                                        @if($canApproveProduksi)
                                            <button type="button" class="btn btn-sm approve-selected-btn" data-type="produksi" style="background-color: #17a2b8; border: none; color: white; font-weight: 500; padding: 6px 14px; border-radius: 4px;">
                                                <i class="fas fa-check mr-1"></i> PROD
                                            </button>
                                        @endif
                                        @if($canApproveSPV)
                                            <button type="button" class="btn btn-sm approve-selected-btn" data-type="spv" style="background-color: #ffc107; border: none; color: #333; font-weight: 500; padding: 6px 14px; border-radius: 4px;">
                                                <i class="fas fa-check mr-1"></i> SPV
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Form Pencarian dengan Pagination Dropdown -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('input-metal-detector.index') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            @if(request()->filled('line'))
                                                <input type="hidden" name="line" value="{{ request('line') }}">
                                            @endif
                                            <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('input-metal-detector.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                        @endif
                                        @if(request()->filled('line'))
                                            <input type="hidden" name="line" value="{{ request('line') }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari data..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('input-metal-detector.index', request()->except(['search', 'page'])) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @include('qc-sistem.input_metal_detector._line_filter')
                            @include('partials.date-range-filter')
                            @include('qc-sistem.input_metal_detector._table')
                        </div>

                        <div class="card-footer d-flex justify-content-center">
                            {{ $metalDetector->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF / Excel Metal Detector Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('input-metal-detector.bulk-export-pdf') }}" method="POST">
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
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift_id">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterLine">Line</label>
                        <select class="form-control" id="filterLine" name="line">
                            <option value="">Semua Line</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Line {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="id_produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
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
                               value="QF 18/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data operasional sesuai filter yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" formaction="{{ route('input-metal-detector.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<style>
.btn-purple {
    background-color: #6f42c1;
    color: white;
    border-color: #6f42c1;
}
.btn-purple:hover {
    background-color: #5a32a3;
    color: white;
    border-color: #5a32a3;
}
</style>
<script>
$(document).ready(function() {
    /**
     * Handle Bulk Approval Button Click
     */
    $(document).on('click', '.bulk-approve-btn', function () {
        var $btn     = $(this);
        var type     = $btn.data('type');
        var tanggal  = $btn.data('tanggal');
        var shift_id = $btn.data('shift-id');
        var line     = $btn.data('line');
        var originalHtml = $btn.html();

        if (!confirm('Apakah Anda yakin ingin menyetujui seluruh data pada shift dan line ini sebagai ' + type.toUpperCase() + '?')) {
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>...');

        $.ajax({
            url    : '{{ route("input-metal-detector.bulk-approve") }}',
            type   : 'POST',
            data   : {
                _token   : '{{ csrf_token() }}',
                type     : type,
                tanggal  : tanggal,
                shift_id : shift_id,
                line     : line
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    alert(response.message);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                alert(msg);
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    /**
     * Handle Single Approval Button Click
     */
    $(document).on('click', '.approve-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);

        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi',
            'spv': 'SPV'
        };

        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i>...');

            $.ajax({
                url: '{{ route("input-metal-detector.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> Verifikasi QC',
                            'produksi': '<i class="fas fa-check"></i> Verifikasi FM/FL PRODUKSI',
                            'spv': '<i class="fas fa-check"></i> Verifikasi SPV'
                        };
                        button.html(originalText[type]);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyetujui data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    button.prop('disabled', false);
                }
            });
        }
    });

    /**
     * Export PDF Filter
     */
    $('[data-bulk-export]').click(function() {
        const button = $(this);
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val();

        if (!kodeForm.trim()) {
            alert('Kode Form harus diisi!');
            return;
        }

        button.prop('disabled', true);
        form.trigger('submit');

        setTimeout(function() {
            button.prop('disabled', false);
        }, 1500);
    });
    
    // ===== CHECKBOX SELECTION LOGIC =====
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

    // Handle individual checkbox
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

    // Handle select-all checkbox
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

    // Handle approval button click
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menyetujui ${selectedUuids.length} data sebagai ${type.toUpperCase()}?`)) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('input-metal-detector.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: selectedUuids
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 800);
                } else {
                    alert('Gagal: ' + response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
            }
        });
    });
});
</script>

<style>
/* Checkbox styling (header & baris data) */
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

/* Approval buttons group styling */
.approval-buttons-group {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.approve-selected-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    transition: all 0.2s ease;
}

.approve-selected-btn:active {
    transform: translateY(0);
}

/* Item row styling */
table.table > tbody > tr.item-row > td {
    vertical-align: middle;
}

.btn-purple { 
    background-color: #6f42c1; 
    color: white; 
    border-color: #6f42c1; 
}

.btn-purple:hover { 
    background-color: #5a32a3; 
    color: white; 
    border-color: #5a32a3; 
}
</style>

@endpush

@endsection