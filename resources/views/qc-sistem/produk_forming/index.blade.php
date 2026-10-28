@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Verifikasi Produk Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Verifikasi Produk Forming</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Verifikasi Produk Forming</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm mr-1" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                @if(auth()->user()->hasPermissionTo('create-produk-forming'))
                                <a href="{{ route('produk-forming.audit') }}" class="btn btn-sm bg-purple mr-1 shadow-sm" style="color: white">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="{{ route('produk-forming.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
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
                                        <i class="fas fa-check mr-1"></i> SPV
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('error') }}
                                </div>
                            @endif

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <!-- Form Pencarian & Per Page -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                            <form action="{{ route('produk-forming.index') }}" method="GET" class="d-inline">
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
                                        <form action="{{ route('produk-forming.index') }}" method="GET">
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
                                                        <a href="{{ route('produk-forming.index', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Clear Search">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width: 40px;">
                                                <input type="checkbox" id="selectAllCheckbox" title="Pilih semua data di halaman ini">
                                            </th>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Produk</th>
                                            <th>Shift</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Status Appr</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $userRole = auth()->user()->id_role;
                                            $rowNumber = ($data->currentPage() - 1) * $data->perPage();
                                        @endphp

                                        @if($data->count() === 0)
                                            <tr>
                                                <td colspan="9" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
                                            </tr>
                                        @endif

                                        @foreach($data as $item)
                                            @php $rowNumber++; @endphp
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih data ini">
                                                </td>
                                                <td>{{ $rowNumber }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                <td>{{ $item->shift->shift ?? '-' }}</td>
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
                                                <td class="text-center">
                                                    <x-action-buttons :item="$item" route-prefix="produk-forming" />
                                                    <x-audit-dropdown :item="$item" route-prefix="produk-forming" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Produk Forming Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('produk-forming.bulk-export-pdf') }}" method="POST">
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
                        <select class="form-control" id="filterShift" name="shift">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk->where('status_bahan', 'forming') as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
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
                               value="QF 20/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data operasional sesuai filter tanggal, shift, dan produk yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" formaction="{{ route('produk-forming.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-download"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<style>
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
#mainTable thead th {
    border-bottom: 2px solid #e2e8f0;
    background-color: #f8fafc;
    color: #475569;
    font-size: 0.75rem;
    vertical-align: middle !important;
}
</style>

<script>
$(document).ready(function() {
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
            url: "{{ route('produk-forming.bulk-approve-selected') }}",
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
});
</script>
@endpush

@endsection
