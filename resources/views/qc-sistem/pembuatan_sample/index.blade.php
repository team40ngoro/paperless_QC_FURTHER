@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Verifikasi Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">Pembuatan Sample</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
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

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title mb-0"><i class="fas fa-vial"></i> Tabel Data Sample</h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group mr-1">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('pembuatan-sample.download-template') }}" class="dropdown-item">
                                                <i class="fas fa-download text-success mr-2"></i> Download Template
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
                                                <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                            </a>
                                        </div>
                                    </div> -->
                                    <button type="button" class="btn btn-danger btn-sm mr-1" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>

                                    <a href="{{ route('pembuatan-sample.audit') }}" class="btn btn-sm btn-purple mr-1">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('create-pembuatan-sample'))
                                        <a href="{{ route('pembuatan-sample.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                                    @endif
                                </div>
                            </div>

                            <!-- Approval Buttons Group: Hidden by default, show when selection > 0 -->
                            <div class="approval-buttons-group d-none mt-2">
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
                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                 <!-- Form Pencarian dengan Pagination Dropdown -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                            <form action="{{ route('pembuatan-sample.index') }}" method="GET" class="d-inline">
                                                @if(request('search'))
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                @endif
                                                <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                    <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-4">
                                        <form action="{{ route('pembuatan-sample.index') }}" method="GET">
                                            @if(request('per_page'))
                                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                            @endif
                                            <div class="input-group shadow-sm">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Produk atau kode produksi..." value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fas fa-search mr-1"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('pembuatan-sample.index', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <table style="white-space: nowrap;" class="table text-center table-bordered table-hover">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center align-middle py-3" style="width: 40px;">
                                                <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                                            </th>
                                            <th class="align-middle">No</th>
                                            <th class="align-middle">Tanggal</th>
                                            <th class="align-middle">Rincian Sample</th>
                                            <th class="align-middle">Status Appr</th>
                                            <th class="align-middle" style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php $no = $data->firstItem(); @endphp
                                        @forelse($groupedData as $groupKey => $groupItems)
                                            @foreach($groupItems as $item)
                                                <tr class="item-row">
                                                    <td class="text-center align-middle">
                                                        <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}">
                                                    </td>
                                                    <td class="align-middle">{{ $no++ }}</td>
                                                    <td class="align-middle">
                                                        <small class="text-muted">
                                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}
                                                        </small>
                                                    </td>
                                                    <td class="text-left align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <span class="badge badge-outline-secondary mr-2">
                                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                                </span>
                                                                <strong>{{ $item->produk->nama_produk ?? '-' }}</strong>
                                                                <span class="mx-2 text-muted">|</span>
                                                                <span class="text-info">{{ $item->jenis_sample }}</span>
                                                                <span class="mx-2 text-muted">|</span>
                                                                <small class="text-muted">Kode: {{ $item->kode_produksi }}</small>
                                                                @if($item->items_json && count($item->items_json) > 0)
                                                                    <span class="mx-2 text-muted">|</span>
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-info toggle-kode-batch-btn"
                                                                        data-target="kode-batch-{{ $item->uuid }}"
                                                                        title="Lihat Detail Kode Batch">
                                                                        <i class="fas fa-barcode mr-1"></i>
                                                                        {{ count($item->items_json) }} Kode Batch
                                                                        <i class="fas fa-chevron-down ml-1 toggle-icon"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        {{-- Collapse: Detail Kode Batch --}}
                                                        @if($item->items_json && count($item->items_json) > 0)
                                                        <div class="kode-batch-collapse mt-2" id="kode-batch-{{ $item->uuid }}" style="display:none;">
                                                            <div class="card card-body p-2 bg-light border-info" style="border-left: 3px solid #17a2b8 !important;">
                                                                <small class="text-muted font-weight-bold mb-2 d-block">
                                                                    <i class="fas fa-list mr-1"></i> Rincian Kode Batch
                                                                </small>
                                                                <table class="table table-sm table-bordered mb-0 bg-white" style="font-size: 0.8rem;">
                                                                    <thead class="thead-dark">
                                                                        <tr>
                                                                            <th class="text-center" style="width:35px;">No</th>
                                                                            <th>Produk</th>
                                                                            <th>Kode Produksi</th>
                                                                            <th>Expired</th>
                                                                            <th class="text-center">Jumlah</th>
                                                                            <th class="text-center">Berat Sampel (g)</th>
                                                                            <th>Jenis Sample</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($item->items_json as $batchIdx => $batch)
                                                                        <tr>
                                                                            <td class="text-center">{{ $batchIdx + 1 }}</td>
                                                                            <td>{{ \App\Models\JenisProduk::find($batch['id_produk'])->nama_produk ?? '-' }}</td>
                                                                            <td><code class="text-danger">{{ $batch['kode_produksi'] ?? '-' }}</code></td>
                                                                            <td>{{ $batch['tanggal_expired'] ?? '-' }}</td>
                                                                            <td class="text-center"><b>{{ $batch['jumlah'] ?? '-' }}</b></td>
                                                                            <td class="text-center">{{ $batch['berat'] ?? '-' }}</td>
                                                                            <td><span class="badge badge-info text-uppercase">{{ $batch['jenis_sample'] ?? '-' }}</span></td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        @endif
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
                                                        <x-action-buttons :item="$item" route-prefix="pembuatan-sample" :isAuditPage="false" />
                                                        <x-audit-dropdown :item="$item" route-prefix="pembuatan-sample" permission-prefix="pembuatan-sample" :uuid="$item->uuid" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
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
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('pembuatan-sample.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <select name="id_shift" class="form-control">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Produk</label>
                        <select name="id_produk" class="form-control select2" style="width: 100%;">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 17/00" value="QF 17/00">
                    </div>
                    <input type="hidden" name="is_audit" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('pembuatan-sample.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-file-pdf mr-2"></i> Export PDF</button>
                    <button type="submit" formaction="{{ route('pembuatan-sample.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('pembuatan-sample.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Pembuatan Sample</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle mr-2"></i> Gunakan template resmi untuk format data yang valid.
                        <a href="{{ route('pembuatan-sample.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
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

<style>
.btn-purple { background-color: #6f42c1; color: white; border-color: #6f42c1; }
.btn-purple:hover { background-color: #5a32a3; color: white; border-color: #5a32a3; }
.btn-xs { padding: 0.15rem 0.4rem; font-size: 0.75rem; line-height: 1.4; border-radius: 0.2rem; }
.badge-outline-secondary { border: 1px solid #6c757d; color: #6c757d; background: transparent; }
.kode-batch-collapse { white-space: normal; }

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

/* Semua sel header & data rata tengah vertikal
   (hanya tabel utama, tidak memengaruhi tabel Rincian Kode Batch) */
table.table > thead > tr > th,
table.table > tbody > tr.item-row > td {
    vertical-align: middle;
}

/* Approval buttons group styling */
.approval-buttons-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.6rem 0.9rem;
    background-color: #eef6fc;
    border: 1px solid #cfe6f5;
    border-radius: 6px;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#exportPdfModal')
        });

        // Checkbox selection logic
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

        // Toggle kode batch collapse
        $(document).on('click', '.toggle-kode-batch-btn', function() {
            const targetId = $(this).data('target');
            const $target = $('#' + targetId);
            const $icon = $(this).find('.toggle-icon');

            $target.slideToggle(200, function() {
                if ($target.is(':visible')) {
                    $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });
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
                url: "{{ route('pembuatan-sample.bulk-approve-selected') }}",
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
                        btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                    Swal.fire('Error!', msg, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
                }
            });
        });
    });
</script>
@endpush
@endsection