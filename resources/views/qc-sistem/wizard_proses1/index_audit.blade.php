@extends('layouts.app')

@section('title', 'Wizard Proses 1 - Audit')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Verifikasi Proses Penggorengan/Pemasakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Wizard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses1.index') }}">Proses 1</a></li>
                        <li class="breadcrumb-item active">Audit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-purple card-outline shadow-sm border-purple">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Data Audit Verifikasi Proses Frying</h3>

                            <div class="card-tools">
                                @if(auth()->user()->id_role !== 6)
                                    <span class="badge p-2 mr-2" style="background-color: #6f42c1; color: white;">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('wizard.proses1.index') }}" class="btn btn-default btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                    </a>
                                @endif
                                <button type="button" class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            @if(auth()->user()->id_role !== 6)
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Info:</strong> Data di halaman ini adalah hasil copy dari data operasional untuk keperluan audit. Perubahan di sini <strong>tidak akan merubah</strong> data operasional asli.
                                </div>
                            @endif

                            <!-- Search & Pagination -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('wizard.proses1.audit') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('wizard.proses1.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Produk" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-purple" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('wizard.proses1.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Data Table -->
                            @if($records->count() > 0)
                            @include('partials.date-range-filter')

                                <div class="table-responsive">
                                    <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                        <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                            <tr>
                                                <th class="text-center py-3" style="width: 50px;">No</th>
                                                <th class="py-3 text-center">Tanggal</th>
                                                <th class="py-3 text-center">Jam</th>
                                                <th class="py-3 text-center">Shift</th>
                                                <th class="py-3 text-left">Kode Produksi</th>
                                                <th class="py-3 text-left">Nama Produk</th>
                                                <th class="py-3 text-center">Dibuat Oleh</th>
                                                <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                                <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                            @php
                                                $no = 1;
                                            @endphp

                                            @forelse($records as $groupKey => $items)
                                                @forelse($items as $item)
                                                    <tr class="item-row">
                                                        <td class="text-center align-middle">{{ $no }}</td>
                                                        <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') : '-' }}</td>
                                                        <td class="text-center align-middle">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                        <td class="text-center align-middle">{{ $item->penggorenganData->shift->shift ?? '-' }}</td>
                                                        <td class="text-left align-middle">{{ $item->penggorenganData->kode_produksi ?? '-' }}</td>
                                                        <td class="text-left align-middle">
                                                            <div style="font-size: 0.85rem;">{{ $item->penggorenganData->produk->nama_produk ?? '-' }}</div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div style="font-size: 0.8rem;">{{ $item->user->name ?? '-' }}</div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="d-flex flex-column align-items-center" style="gap: 2px;">
                                                                @if($item->approved_by_qc)
                                                                    <span class="badge badge-success w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> QC</span>
                                                                @else
                                                                    <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">QC</span>
                                                                @endif
                                                                @if($item->approved_by_produksi)
                                                                    <span class="badge badge-primary w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> PRD</span>
                                                                @else
                                                                    <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">PRD</span>
                                                                @endif
                                                                @if($item->approved_by_spv)
                                                                    <span class="badge badge-dark w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> SPV</span>
                                                                @else
                                                                    <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">SPV</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <x-action-buttons
                                                                :item="$item"
                                                                route-prefix="wizard.proses1"
                                                                permission-prefix="wizard-proses1"
                                                                :show-history="false"
                                                                :show-view="true"
                                                                :show-print="true"
                                                                class="tombol"
                                                            >
                                                                {{-- Tombol Collapse Rincian --}}
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-info btn-rincian-toggle tombol"
                                                                    data-target="#rincian-{{ $item->uuid }}"
                                                                    title="Lihat Rincian Langkah"
                                                                    style="border-radius:6px;">
                                                                    <i class="fas fa-list-ul"></i>
                                                                </button>
                                                            </x-action-buttons>
                                                        </td>        
                                                    </tr>
                                                    <tr id="rincian-{{ $item->uuid }}" class="rincian-row" style="display:none;">
                                                        <td colspan="8" class="p-0">
                                                            <div class="p-3 bg-light border-bottom">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <h6 class="font-weight-bold text-info mb-0">
                                                                        <i class="fas fa-stream mr-1"></i> Rincian Langkah Proses
                                                                    </h6>
                                                                    <a href="{{ route('wizard.proses1.show', [$item->uuid, 'is_audit' => 1]) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye mr-1"></i> Lihat Rincian Lengkap
                                                                    </a>
                                                                </div>
                                                                @php
                                                                    $fryerRecords = [
                                                                        ['Fryer 1', $item->frayerData, 'suhu_frayer', 'waktu_penggorengan', 'suhuFrayer', 'waktuPenggorengan'],
                                                                        ['Fryer 2', $item->frayer2Data, 'suhu_frayer_2', 'waktu_penggorengan_2', 'suhuFrayer2', 'waktuPenggorengan2'],
                                                                        ['Fryer 3', $item->frayer3Data, 'suhu_frayer_3', 'waktu_penggorengan_3', 'suhuFrayer', 'suhuFrayer'],
                                                                        ['Fryer 4', $item->frayer4Data, 'suhu_frayer_4', 'waktu_penggorengan_4', 'suhuFrayer', 'suhuFrayer'],
                                                                        ['Fryer 5', $item->frayer5Data, 'suhu_frayer_5', 'waktu_penggorengan_5', 'suhuFrayer', 'suhuFrayer'],
                                                                    ];
                                                                    $fryerRecords = array_values(array_filter($fryerRecords, fn ($row) => $row[1]));
                                                                    $hasilGoreng = $item->hasilPenggorenganData;
                                                                    $stdSuhuPusat = $hasilGoreng->stdSuhuPusat->std_suhu_pusat ?? [];
                                                                    $stdSuhuPusat = is_array($stdSuhuPusat) ? $stdSuhuPusat : [$stdSuhuPusat];
                                                                    $stdSuhuIndex = $item->frayer5Data ? 4 : ($item->frayer4Data ? 3 : ($item->frayer3Data ? 2 : ($item->frayer2Data ? 1 : 0)));
                                                                    $stdSuhuPusatValue = $stdSuhuPusat[$stdSuhuIndex] ?? ($stdSuhuPusat[0] ?? '-');
                                                                    $suhuPusatRaw = $hasilGoreng->aktual_suhu_pusat ?? '';
                                                                    if (is_string($suhuPusatRaw)) {
                                                                        $decodedSuhuPusat = json_decode($suhuPusatRaw, true);
                                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedSuhuPusat)) {
                                                                            $suhuPusatRaw = $decodedSuhuPusat;
                                                                        }
                                                                    }
                                                                    $suhuPusatActuals = [];
                                                                    foreach ((array) $suhuPusatRaw as $value) {
                                                                        if (is_numeric($value)) {
                                                                            $suhuPusatActuals[] = $value;
                                                                        } elseif (is_string($value) && $value !== '') {
                                                                            preg_match_all('/\d+[.,]\d+|\d+/', $value, $matches);
                                                                            foreach ($matches[0] as $match) {
                                                                                $suhuPusatActuals[] = str_replace(',', '.', $match);
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                <div class="row">
                                                                    <div class="col-lg-8 mb-3">
                                                                        <div class="card mb-0 h-100">
                                                                            <div class="card-header py-2"><strong>Data Aktual Fryer</strong></div>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-sm table-bordered mb-0">
                                                                                    <thead class="text-center">
                                                                                        <tr>
                                                                                            <th>Mesin</th><th>Suhu Std</th><th>Waktu Std</th><th>Suhu Aktual</th><th>Waktu Aktual</th><th>TPM</th><th>FFA</th><th>Keterangan</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @forelse($fryerRecords as [$label, $record, $suhuField, $waktuField, $suhuRelation, $waktuRelation])
                                                                                            <tr class="text-center">
                                                                                                <td>{{ $label }}</td>
                                                                                                <td>{{ data_get($record, $suhuRelation . '.' . $suhuField, '-') }} °C</td>
                                                                                                <td>{{ data_get($record, $waktuRelation . '.' . $waktuField, '-') }} dtk</td>
                                                                                                <td>{{ $record->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                                                                <td>{{ $record->aktual_penggorengan ?? '-' }} dtk</td>
                                                                                                <td>{{ $record->tpm_minyak ?? '-' }} %</td>
                                                                                                <td>{{ $record->ffa ?? '-' }}</td>
                                                                                                <td>{{ $record->keterangan ?? '-' }}</td>
                                                                                            </tr>
                                                                                        @empty
                                                                                            <tr><td colspan="8" class="text-center text-muted">Data fryer tidak tersedia</td></tr>
                                                                                        @endforelse
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4 mb-3">
                                                                        <div class="card mb-0 h-100">
                                                                            <div class="card-header py-2"><strong>Suhu Pusat</strong></div>
                                                                            <div class="card-body">
                                                                                <div class="mb-3">
                                                                                    <div class="small text-muted">STD Suhu Pusat</div>
                                                                                    <strong>{{ $stdSuhuPusatValue }} °C</strong>
                                                                                </div>
                                                                                <div class="small text-muted mb-1">Suhu Pusat Aktual</div>
                                                                                @forelse($suhuPusatActuals as $temperature)
                                                                                    <span class="badge badge-secondary mr-1 mb-1">{{ $temperature }} °C</span>
                                                                                @empty
                                                                                    <span>-</span>
                                                                                @endforelse
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                @empty
                                                @endforelse
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-5 text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        <p>Tidak ada data audit</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Tidak ada data audit yang tersedia.
                                </div>
                            @endif

                            <div class="mt-4 d-flex justify-content-between align-items-center" id="paginationWrapper">
                                <div id="paginationInfo" class="text-muted small">
                                    Menampilkan {{ $records->firstItem() ?? 0 }} sampai {{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} data
                                </div>
                                <div id="paginationLinks">
                                    {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Filter Cetak PDF / Excel -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('wizard.proses1.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF / Excel Proses 1 Frying (Audit)</h5>
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
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Produk</label>
                        <div class="input-group">
                            <select name="produk_id" class="form-control" id="pdf_produk_filter_audit">
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
                            <input type="text" name="kode_form" class="form-control" value="QF 09/00" required>
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('wizard.proses1.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" formaction="{{ route('wizard.proses1.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-rincian-toggle', function() {
            const $button = $(this);
            const $row = $($button.data('target'));
            const isOpen = $row.is(':visible');

            $row.toggle(!isOpen);
            $button.toggleClass('active', !isOpen)
                .attr('title', isOpen ? 'Lihat Rincian Langkah' : 'Tutup Rincian')
                .html(isOpen ? '<i class="fas fa-list-ul"></i>' : '<i class="fas fa-times"></i>');
        });
    });
</script>
@endpush
