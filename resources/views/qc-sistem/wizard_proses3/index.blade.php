@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Verifikasi Proses Roasted Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses3.index') }}">Wizard</a></li>
                        <li class="breadcrumb-item active">Verifikasi Proses Roasted Produk</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-fire text-danger"></i> Verifikasi Proses Roasted Produk</h3>
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
                                    <i class="fas fa-filter text-danger"></i> Berdasarkan Filter
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wizard.proses3.audit') }}" class="btn btn-sm btn-purple mr-1">
                            <i class="fas fa-history"></i>
                        </a>
                        @if(auth()->user()->hasPermissionTo('create-wizard-proses3'))
                        <a href="{{ route('wizard.proses3.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Input Proses Baru
                        </a>
                        
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

                    <!-- Form Pencarian -->
                    <div class="row mb-3 mt-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                <form action="{{ route('wizard.proses3.index') }}" method="GET" class="d-inline">
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
                            <form action="{{ route('wizard.proses3.index') }}" method="GET">
                                @if(request('per_page'))
                                    <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                @endif
                                <div class="input-group shadow-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Cari Produk" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search mr-1"></i> Cari
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('wizard.proses3.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($pembekuanData->count() > 0)
                    @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'wizard.proses3.bulk-approve-selected'])
                            @include('partials.date-range-filter')

                    <div class="table-responsive">
                        <table id="bulkApprovalTable" class="table table-bordered table-striped text-center" style="white-space:nowrap;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="bulkApprovalSelectAll" title="Pilih semua"></th>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Shift</th>
                                    <th>Produk</th>
                                    <th>Kode Produksi</th>
                                    <th>Aktual Lama Proses</th>
                                    <th>Aktual Suhu Pusat Produk (ºC)</th>
                                    <th>Dibuat oleh</th>
                                    <th>Status Appr</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembekuanData->getCollection()->flatten(1) as $item)
                                    <tr>
                                        <td class="align-middle">
                                            <input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
                                        </td>
                                        <td>{{ $pembekuanData->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->tanggal ? $item->tanggal->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $item->inputRoastingData->jam ?? ($item->jam ?? '-') }}</td>
                                        <td>{{ $item->shift->shift ?? '-' }}</td>
                                        <td class="text-left"><strong>{{ $item->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</strong></td>
                                        <td><span class="badge badge-light border">{{ $item->inputRoastingData->kode_produksi ?? '-' }}</span></td>
                                        <td>{{ $item->prosesRoastingFanData->aktual_lama_proses ?? '-' }}</td>
                                        <td>
                                            @php
                                                $suhuPusatRaw = $item->hasilProsesRoastingData->aktual_suhu_pusat ?? '';
                                                $suhuPusatList = [];
                                                
                                                if (is_array($suhuPusatRaw)) {
                                                    foreach ($suhuPusatRaw as $val) {
                                                        if (is_string($val) && $val !== '') {
                                                            preg_match_all('/\d+[.,]\d+|\d+/', $val, $matches);
                                                            foreach ($matches[0] as $match) {
                                                                $suhuPusatList[] = (float) str_replace(',', '.', $match);
                                                            }
                                                        } elseif (is_numeric($val)) {
                                                            $suhuPusatList[] = (float) str_replace(',', '.', $val);
                                                        }
                                                    }
                                                } elseif (is_string($suhuPusatRaw) && $suhuPusatRaw !== '') {
                                                    preg_match_all('/\d+[.,]\d+|\d+/', $suhuPusatRaw, $matches);
                                                    $suhuPusatList = array_map(fn($v) => (float) str_replace(',', '.', $v), $matches[0]);
                                                }
                                            @endphp
                                            @if(!empty($suhuPusatList))
                                                @foreach($suhuPusatList as $suhuVal)
                                                    <span class="badge badge-success mr-1 mb-1">{{ $suhuVal }} °C</span><br>
                                                @endforeach
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $item->user->name ?? '-' }}</small></td>
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
                                            <x-action-buttons
                                                :item="$item"
                                                route-prefix="wizard.proses3"
                                                permission-prefix="wizard-proses3"
                                                :show-history="false"
                                                :show-view="true"
                                                :show-print="true"
                                            >
                                                {{-- Tombol Collapse Rincian --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info btn-rincian-toggle"
                                                    data-target="#rincian-{{ $item->uuid }}"
                                                    title="Lihat Rincian Langkah"
                                                    style="border-radius:6px;">
                                                    <i class="fas fa-list-ul"></i>
                                                </button>
                                            </x-action-buttons>
                                            <x-audit-dropdown :item="$item" route-prefix="wizard.proses3" permission-prefix="wizard-proses3" :uuid="$item->uuid" />
                                        </td>
                                    </tr>

                                    {{-- ROW RINCIAN COLLAPSE --}}
                                    <tr id="rincian-{{ $item->uuid }}" class="rincian-row" style="display:none;">
                                        <td colspan="12" class="p-0 border-0">
                                            <div class="rincian-wrapper p-3" style="background:#f8f9fc; border-left:4px solid #17a2b8; border-bottom:1px solid #dee2e6;">
                                                <h6 class="font-weight-bold text-info mb-3">
                                                    <i class="fas fa-stream mr-1"></i>
                                                    Rincian Langkah Proses &mdash; <span class="text-dark">{{ $item->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</span>
                                                    <small class="text-muted ml-2">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }} {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '' }}</small>
                                                </h6>

                                                <div class="row">
                                                    {{-- STANDAR ROASTING FAN: Suhu Blok, Infeed/Outfeed, Parameter Pemasakan, Humidity --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="card border-0 shadow-sm h-100" style="border-radius:8px; border-left:3px solid #e83e8c !important;">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge mr-2" style="font-size:0.7rem; border-radius:50%; width:22px; height:22px; display:flex; align-items:center; justify-content:center; background:#e83e8c; color:#fff;"><i class="fas fa-fan" style="font-size:0.6rem;"></i></span>
                                                                    <strong style="color:#e83e8c; font-size:0.85rem;">Standar Roasting Fan</strong>
                                                                </div>
                                                                @if($item->prosesRoastingFanData)
                                                                    @php
                                                                        $blokData3 = $item->prosesRoastingFanData->blok_data;
                                                                        if (is_string($blokData3)) $blokData3 = json_decode($blokData3, true) ?: [];
                                                                        $blokData3 = collect($blokData3 ?? [])->sortBy('block_number');
                                                                        $firstBlok3 = $blokData3->first();
                                                                        $stdFanObj3 = $firstBlok3 ? \App\Models\StdFan::find($firstBlok3['id_std_fan'] ?? null) : null;
                                                                    @endphp
                                                                    <table class="table table-sm table-borderless mb-0" style="font-size:0.78rem;">
                                                                        {{-- STD Lama Proses (parameter pemasakan) --}}
                                                                        @if($stdFanObj3)
                                                                        <tr>
                                                                            <td class="text-muted py-0 pl-0" style="width:55%">STD Lama Proses</td>
                                                                            <td class="py-0"><span class="badge badge-light border text-dark">{{ $stdFanObj3->std_lama_proses ?? '-' }} menit</span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted py-0 pl-0">STD Humidity</td>
                                                                            <td class="py-0"><span class="badge badge-light border text-dark">{{ $stdFanObj3->std_humadity ?? '-' }} %</span></td>
                                                                        </tr>
                                                                        @endif
                                                                        {{-- STD Suhu per Blok (infeed/outfeed per blok) --}}
                                                                        @foreach($blokData3 as $blok3)
                                                                            @php $stdSuhuBlok3 = \App\Models\SuhuBlok::find($blok3['id_suhu_blok'] ?? null); @endphp
                                                                            @if($stdSuhuBlok3)
                                                                            <tr>
                                                                                <td class="text-muted py-0 pl-0">STD Suhu Blok {{ $blok3['block_number'] ?? ($loop->index+1) }}</td>
                                                                                <td class="py-0"><span class="badge badge-light border text-dark">{{ $stdSuhuBlok3->suhu_blok ?? '-' }} °C</span></td>
                                                                            </tr>
                                                                            @endif
                                                                        @endforeach
                                                                        {{-- Conveyor Infeed / Outfeed --}}
                                                                        @if($item->prosesRoastingFanData->conveyor_infeed)
                                                                        <tr>
                                                                            <td class="text-muted py-0 pl-0">Conveyor Infeed</td>
                                                                            <td class="py-0 font-weight-bold">{{ $item->prosesRoastingFanData->conveyor_infeed }}</td>
                                                                        </tr>
                                                                        @endif
                                                                        @if($item->prosesRoastingFanData->conveyor_outfeed)
                                                                        <tr>
                                                                            <td class="text-muted py-0 pl-0">Conveyor Outfeed</td>
                                                                            <td class="py-0 font-weight-bold">{{ $item->prosesRoastingFanData->conveyor_outfeed }}</td>
                                                                        </tr>
                                                                        @endif
                                                                        @if($item->prosesRoastingFanData->conveyor_blok1)
                                                                        <tr>
                                                                            <td class="text-muted py-0 pl-0">Conveyor Blok 1</td>
                                                                            <td class="py-0 font-weight-bold">{{ $item->prosesRoastingFanData->conveyor_blok1 }}</td>
                                                                        </tr>
                                                                        @endif
                                                                    </table>
                                                                @else
                                                                    <span class="text-muted small"><i class="fas fa-minus-circle mr-1"></i>Tidak ada data</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- SUHU PUSAT STD --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="card border-0 shadow-sm h-100" style="border-radius:8px; border-left:3px solid #6f42c1 !important;">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <span class="badge mr-2" style="font-size:0.7rem; border-radius:50%; width:22px; height:22px; display:flex; align-items:center; justify-content:center; background:#6f42c1; color:#fff;"><i class="fas fa-thermometer-half" style="font-size:0.6rem;"></i></span>
                                                                    <strong style="color:#6f42c1; font-size:0.85rem;">Suhu Pusat STD</strong>
                                                                </div>
                                                                @php
                                                                    $stdSp3 = $item->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? null;
                                                                @endphp
                                                                @if($stdSp3)
                                                                    <div class="text-center py-2">
                                                                        <span class="badge badge-light border text-dark" style="font-size:1rem; padding:8px 16px;">{{ $stdSp3 }} °C</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted small"><i class="fas fa-minus-circle mr-1"></i>Tidak ada data</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>{{-- end .row --}}
                                            </div>{{-- end .rincian-wrapper --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pembekuanData->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Belum ada data riwayat Roasted Product. Silakan input data baru.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- CSS untuk Sequential Approval -->
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
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-outline-dark:hover {
    background-color: #343a40;
    border-color: #343a40;
}
.btn-vertical {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.btn-vertical .mb-1 {
    margin-bottom: 4px;
}
/* Rincian collapse row */
.rincian-row td {
    padding: 0 !important;
}
.rincian-wrapper {
    animation: fadeInDown 0.2s ease;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.btn-rincian-toggle.active {
    background-color: #17a2b8;
    color: #fff;
    border-color: #17a2b8;
}
</style>

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('wizard.proses3.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-file-pdf mr-2"></i>Cetak PDF / Excel Roasted Product</h5>
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
                                    $pdfShifts = auth()->user()->role === 'superadmin'
                                        ? \App\Models\DataShift::orderBy('shift')->get()
                                        : \App\Models\DataShift::where('id_plan', auth()->user()->id_plan)->orderBy('shift')->get();
                                @endphp
                                @foreach($pdfShifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Produk</label>
                        <div class="input-group">
                            <select name="produk_id" class="form-control" id="pdf_produk_filter_3">
                                <option value="">Semua Produk</option>
                                 @php
                                    $pdfProduks = auth()->user()->role === 'superadmin'
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
                            <input type="text" name="kode_form" class="form-control" value="QF 10/00" required>
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('wizard.proses3.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" formaction="{{ route('wizard.proses3.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@include('partials.bulk-approval-selected-script')
<script>
$(document).ready(function() {
    // Single Approval Handler
    $(document).on('click', '.approve-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);

        const typeNames = { 'qc': 'QC', 'produksi': 'Produksi', 'spv': 'SPV' };

        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route("wizard.proses3.approve", ":id") }}'.replace(':id', id),
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

});
</script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle Rincian Langkah
        $(document).on('click', '.btn-rincian-toggle', function() {
            const target = $(this).data('target');
            const $row = $(target);
            const $btn = $(this);

            if ($row.is(':visible')) {
                $row.hide();
                $btn.removeClass('active').attr('title', 'Lihat Rincian Langkah');
                $btn.html('<i class="fas fa-list-ul"></i>');
            } else {
                $row.show();
                $btn.addClass('active').attr('title', 'Tutup Rincian');
                $btn.html('<i class="fas fa-times"></i>');
            }
        });

        $('#pdf_produk_filter_3').select2({
            dropdownParent: $('#exportPdfModal'),
            placeholder: 'Pilih Produk',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
@endsection