@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pemeriksaan Labelisasi Start Up</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Pemeriksaan Labelisasi Start Up</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Data Pemeriksaan Labelisasi Start Up</h3>
                    <div class="card-tools">
                        <!-- <div class="btn-group mr-1">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                <i class="fas fa-file-excel mr-1"></i> Import Excel
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="{{ route('label-pemeriksaan.download-template') }}" class="dropdown-item">
                                    <i class="fas fa-download text-success mr-2"></i> Download Template
                                </a>
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
                                    <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                </a>
                            </div>
                        </div>
                        <div class="btn-group mr-1"> -->
                            <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('label-pemeriksaan.audit') }}" class="btn btn-sm btn-purple mr-1">
                            <i class="fas fa-history"></i>
                        </a>
                        @php
                            $userRole = (int) (auth()->user()->id_role ?? 0);
                            $canCreateLabel = $userRole !== 3 && (in_array($userRole, [1, 2, 5], true) || auth()->user()->hasPermissionTo('create-label-pemeriksaan'));
                        @endphp
                        @if($canCreateLabel)
                        <a href="{{ route('label-pemeriksaan.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Data (Produksi)
                        </a>
                        @endif
                    </div>
                </div>

                <div class="approval-buttons-group d-none" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; margin: 15px 15px 0 15px; padding: 12px 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    @php
                        $userRole = auth()->user()->id_role;
                        $canApproveQc = in_array($userRole, [1, 3, 5]);
                        $canApproveProduksi = in_array($userRole, [1, 2, 5]);
                        $canApproveSPV = in_array($userRole, [1, 4, 5]);
                    @endphp
                    
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
                                <i class="fas fa-check mr-1"></i> SPV QC
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
                            <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                            @include('partials.date-range-filter')

                    <div class="table-responsive">
                        {{-- Form Pencarian --}}
                        <div class="row mb-3 mt-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                    <form action="{{ route('label-pemeriksaan.index') }}" method="GET" class="d-inline">
                                        @if($search)
                                            <input type="hidden" name="search" value="{{ $search }}">
                                        @endif
                                        <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                            @foreach([5, 10, 25, 50, 100] as $pageSize)
                                                <option value="{{ $pageSize }}" {{ ($perPage ?? 10) == $pageSize ? 'selected' : '' }}>{{ $pageSize }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4 offset-md-4">
                                <form action="{{ route('label-pemeriksaan.index') }}" method="GET">
                                    @if(request('per_page'))
                                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                                    @endif
                                    <div class="input-group shadow-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search mr-1"></i> Cari
                                            </button>
                                            @if(!empty($search))
                                                <a href="{{ route('label-pemeriksaan.index', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Clear Search">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="table-label">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" id="selectAllCheckbox" title="Pilih semua data di halaman ini">
                                    </th>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Jam</th>
                                    <th>Plant</th>
                                    <th>Nama Produk</th>
                                    <th>Label</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Status Appr</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $userRole = auth()->user()->id_role;
                                    $rowNumber = 0;
                                @endphp

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
                                    </tr>
                                @endif

                                @foreach($data as $item)
                                    @foreach($item->details as $detail)
                                        @php $rowNumber++; @endphp
                                        <tr>
                                            @if($loop->first)
                                                <td class="text-center align-middle" rowspan="{{ max($item->details->count(), 1) }}">
                                                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih data ini">
                                                </td>
                                            @endif
                                            <td>{{ $rowNumber }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                                <td>{{ $item->shift->shift ?? '-' }}</td>
                                                <td>{{ $detail->jam ?? '-' }}</td>
                                                <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $detail->produk->nama_produk ?? '-' }}</strong>
                                                        @if($detail->berat)
                                                            <span class="badge badge-secondary ml-1">{{ rtrim(rtrim(number_format($detail->berat, 2), '0'), '.') }} gram</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($detail->label_produk)
                                                        <span class="badge badge-info">{{ $detail->label_produk }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $item->user->name ?? '-' }}</td>
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
                                                    <x-action-buttons :item="$item" class="tombol" route-prefix="label-pemeriksaan" :show-print="true" :isAuditPage="false" />
                                                    <x-audit-dropdown :item="$item" route-prefix="label-pemeriksaan" permission-prefix="label-pemeriksaan" :uuid="$item->uuid" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Modal Export PDF --}}
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('label-pemeriksaan.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel">
                        <i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <select name="shift_id" class="form-control">
                            <option value="">Semua Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Produk</label>
                        <select name="produk_id" class="form-control select2" style="width: 100%;">
                            <option>Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 36/00" value="QF 36/00">
                    </div>
                    <input type="hidden" name="is_audit" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('label-pemeriksaan.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-file-pdf mr-2"></i> Export PDF</button>
                    <button type="submit" formaction="{{ route('label-pemeriksaan.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('label-pemeriksaan.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Pemeriksaan Label</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle mr-2"></i> Gunakan template resmi untuk format data yang valid.
                        <a href="{{ route('label-pemeriksaan.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Pilih File Excel</label>
                        <div class="custom-file shadow-sm">
                            <input type="file" class="custom-file-input" id="excelFile" name="file" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="excelFile">Pilih file...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-upload mr-2"></i> Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update file label when file is selected
    $('#excelFile').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih file...');
    });
</script>
@endpush

@push('scripts')
<style>
.btn-purple { background-color: #6f42c1; color: white; border-color: #6f42c1; }
.btn-purple:hover { background-color: #5a32a3; color: white; border-color: #5a32a3; }
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

#selectAllCheckbox,
.row-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
</style>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#exportPdfModal')
    });

    $(document).on('click', '.approve-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);
        const typeNames = { 'qc': 'QC', 'produksi': 'Produksi', 'spv': 'SPV' };

        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route("label-pemeriksaan.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Gagal: ' + response.message);
                        button.prop('disabled', false).html('<i class="fas fa-check"></i> ' + typeNames[type]);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan');
                    button.prop('disabled', false).html('<i class="fas fa-check"></i> ' + typeNames[type]);
                }
            });
        }
    });

    const selectedUuids = new Set();
    const $approvalGroup = $('.approval-buttons-group');

    function updateSelectionUi() {
        $('.selected-count').text(selectedUuids.size);
        $approvalGroup.toggleClass('d-none', selectedUuids.size === 0);

        const totalCheckboxes = $('.row-checkbox').length;
        $('#selectAllCheckbox').prop('checked', totalCheckboxes > 0 && selectedUuids.size === totalCheckboxes);
    }

    $(document).on('change', '.row-checkbox', function() {
        const uuid = String($(this).data('uuid'));
        if (this.checked) {
            selectedUuids.add(uuid);
        } else {
            selectedUuids.delete(uuid);
        }
        updateSelectionUi();
    });

    $('#selectAllCheckbox').on('change', function() {
        const checked = this.checked;
        $('.row-checkbox').each(function() {
            this.checked = checked;
            const uuid = String($(this).data('uuid'));
            if (checked) {
                selectedUuids.add(uuid);
            } else {
                selectedUuids.delete(uuid);
            }
        });
        updateSelectionUi();
    });

    $(document).on('click', '.approve-selected-btn', function() {
        const $button = $(this);
        const type = $button.data('type');
        const uuids = Array.from(selectedUuids);
        const typeNames = { qc: 'QC', produksi: 'Produksi', spv: 'SPV' };

        if (uuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }
        if (!confirm(`Apakah Anda yakin ingin menyetujui ${uuids.length} data sebagai ${typeNames[type]}?`)) {
            return;
        }

        const originalHtml = $button.html();
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        $.ajax({
            url: "{{ route('label-pemeriksaan.bulk-approve-selected') }}",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                type: type,
                uuids: uuids
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                    return;
                }

                alert('Gagal: ' + (response.message || 'Approval tidak berhasil.'));
                $button.prop('disabled', false).html(originalHtml);
            },
            error: function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Terjadi kesalahan saat memproses approval.';
                alert(message);
                $button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@endsection
