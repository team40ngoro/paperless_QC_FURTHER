@extends('layouts.app')

@section('title', 'Wizard Proses 1 - Penggorengan/Pemasakan')

@section('container')
@php
    $userRole = (int) (auth()->user()->id_role ?? 0);
    $canCreateProses1 = $userRole !== 3 && (in_array($userRole, [1, 2, 5], true) || auth()->user()->hasPermissionTo('create-wizard-proses1'));
    $canApproveQc = in_array($userRole, [1, 3, 5], true);
    $canApproveSPV = in_array($userRole, [1, 4, 5], true);
    $showApprovalCheckbox = $canApproveQc || $canApproveSPV;
    $tableColspan = $showApprovalCheckbox ? 11 : 10;
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-hot-tub text-warning"></i> Verifikasi Proses Penggorengan/Pemasakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.selection') }}"><i class="fas fa-home"></i> Wizard</a></li>
                        <li class="breadcrumb-item active">Frying (Proses 1)</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0">
                                    <i class="fas fa-list text-success mr-2"></i> Data Verifikasi Proses Frying
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <a href="{{ route('wizard.selection') }}" class="btn btn-secondary btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
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
                                    <a href="{{ route('wizard.proses1.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Riwayat Audit">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if($canCreateProses1)
                                        <a href="{{ route('wizard.proses1.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Input Proses Baru (Produksi)
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($showApprovalCheckbox)
                            <!-- Bar aksi bulk approve: hanya muncul saat ada baris yang dicentang (Alur: QC Inspector -> SPV QC) -->
                            <div class="approval-buttons-group d-none">
                                <span class="badge badge-info"><span class="selected-count">0</span> dipilih</span>

                                @if($canApproveQc)
                                    <button type="button" class="btn btn-sm btn-success shadow-sm approve-selected-btn" data-type="qc">
                                        <i class="fas fa-check mr-1"></i> Approve QC Inspector
                                    </button>
                                @endif
                                @if($canApproveSPV)
                                    <button type="button" class="btn btn-sm btn-warning shadow-sm approve-selected-btn" data-type="spv" style="color: white;">
                                        <i class="fas fa-check mr-1"></i> Approve SPV QC
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
                            @if(session('info'))
                                <div class="alert alert-info alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-info-circle"></i> {{ session('info') }}
                                </div>
                            @endif

                            @if($userRole === 3)
                                <div class="alert alert-info border-0 shadow-sm mb-3">
                                    <i class="fas fa-eye mr-1"></i>
                                    <strong>Petunjuk QC Inspector:</strong> Klik tombol <strong>Ikon Mata (<i class="fas fa-eye"></i>)</strong> pada kolom Aksi untuk membaca data yang telah diinput oleh Produksi terlebih dahulu dan mengisi verifikasi QC (Pencetakan, Predust, Battering, Breading, Frying, dan Hasil Penggorengan).
                                </div>
                            @endif

                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('wizard.proses1.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('wizard.proses1.index') }}" method="GET">
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
                                                    <a href="{{ route('wizard.proses1.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                            @if($showApprovalCheckbox)
                                            <th class="text-center py-3" style="width: 40px;">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih semua">
                                                </div>
                                            </th>
                                            @endif
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-left">Kode Produksi</th>
                                            <th class="py-3 text-left">Nama Produk</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center">Status Verifikasi QC</th>
                                            <th class="py-3 text-center" style="width: 110px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $no = 1;
                                        @endphp

                                        @forelse($pembekuanData as $groupKey => $items)
                                            @forelse($items as $item)
                                                @php
                                                    $isQcVerified = !empty($item->hasil_penggorengan_uuid) && !empty(optional($item->penggorenganData)->hasil_pencetakan);
                                                @endphp
                                                <tr class="item-row">
                                                    @if($showApprovalCheckbox)
                                                    <td class="text-center align-middle">
                                                        <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}">
                                                    </td>
                                                    @endif
                                                    <td class="text-center align-middle">{{ $no }}</td>
                                                    <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') : '-' }}</td>
                                                    <td class="text-center align-middle">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                    <td class="text-center align-middle">{{ $item->penggorenganData->shift->shift ?? '-' }}</td>
                                                    <td class="text-left align-middle">{{ $item->penggorenganData->kode_produksi ?? '-' }}</td>
                                                    <td class="text-left align-middle">
                                                        <div style="font-size: 0.85rem;">{{ $item->penggorenganData->produk->nama_produk ?? $item->hasilPenggorenganData->produk->nama_produk ?? '-' }}</div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div style="font-size: 0.8rem;">{{ $item->user->name ?? '-' }}</div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        @if($isQcVerified)
                                                            <span class="badge badge-success px-2 py-1" style="font-size: 0.72rem;">
                                                                <i class="fas fa-check-circle mr-1"></i> Sudah Diisi QC
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning px-2 py-1" style="font-size: 0.72rem;">
                                                                <i class="fas fa-clock mr-1"></i> Menunggu QC
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="d-flex flex-column align-items-center" style="gap: 3px;">
                                                            @if($item->approved_by_qc)
                                                                <span class="badge badge-success w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> QC</span>
                                                            @elseif($canApproveQc)
                                                                <button type="button" class="btn btn-xs btn-outline-success w-100 single-approve-btn" data-uuid="{{ $item->uuid }}" data-type="qc" style="font-size: 0.65rem; padding: 1px 4px;" title="Approve sebagai QC Inspector">
                                                                    <i class="fas fa-check"></i> Appr QC
                                                                </button>
                                                            @else
                                                                <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">QC</span>
                                                            @endif

                                                            @if($item->approved_by_spv)
                                                                <span class="badge badge-dark w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> SPV</span>
                                                            @elseif($item->approved_by_qc && $canApproveSPV)
                                                                <button type="button" class="btn btn-xs btn-outline-dark w-100 single-approve-btn" data-uuid="{{ $item->uuid }}" data-type="spv" style="font-size: 0.65rem; padding: 1px 4px;" title="Approve sebagai SPV QC">
                                                                    <i class="fas fa-check"></i> Appr SPV
                                                                </button>
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
                                                        @if(!isset($isAuditPage) || !$isAuditPage)
                                                            <x-audit-dropdown :item="$item" route-prefix="wizard.proses1" permission-prefix="wizard-proses1" :uuid="$item->uuid" />
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr id="rincian-{{ $item->uuid }}" class="rincian-row" style="display:none;">
                                                    <td colspan="{{ $tableColspan }}" class="p-0">
                                                        <div class="p-3 bg-light border-bottom">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h6 class="font-weight-bold text-info mb-0">
                                                                    <i class="fas fa-stream mr-1"></i> Rincian Langkah Proses
                                                                </h6>
                                                                <a href="{{ route('wizard.proses1.show', $item->uuid) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye mr-1"></i> {{ $userRole === 3 ? 'Buka & Input Verifikasi QC' : 'Lihat Rincian Lengkap' }}
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
                                                                        <div class="card-header py-2"><strong>Data Aktual Fryer (QC)</strong></div>
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
                                                                                        <tr><td colspan="8" class="text-center text-muted">Data fryer belum diisi oleh QC Inspector</td></tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-3">
                                                                    <div class="card mb-0 h-100">
                                                                        <div class="card-header py-2"><strong>Suhu Pusat (QC)</strong></div>
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
                                                <td colspan="{{ $tableColspan }}" class="text-center py-5 text-muted">
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
                                {{ $pembekuanData->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF / Excel Proses 1 Frying</h5>
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
                            <select name="produk_id" class="form-control" id="pdf_produk_filter">
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
                            <input type="text" name="kode_form" class="form-control" value="QF 09/00" required>
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="{{ request()->routeIs('wizard.proses1.audit') ? 1 : 0 }}">
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
$(document).ready(function() {
    let selectedUuids = [];

    $(document).on('click', '.btn-rincian-toggle', function() {
        const $button = $(this);
        const $row = $($button.data('target'));
        const isOpen = $row.is(':visible');

        $row.toggle(!isOpen);
        $button.toggleClass('active', !isOpen)
            .attr('title', isOpen ? 'Lihat Rincian Langkah' : 'Tutup Rincian')
            .html(isOpen ? '<i class="fas fa-list-ul"></i>' : '<i class="fas fa-times"></i>');
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

    // Single Row Approval Handler
    $(document).on('click', '.single-approve-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const uuid = btn.data('uuid');
        const type = btn.data('type');
        const label = type === 'spv' ? 'SPV QC' : 'QC Inspector';

        if (!confirm('Apakah Anda yakin ingin menyetujui data ini sebagai ' + label + '?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ url('qc-sistem/wizard/proses1') }}/" + uuid + "/approve",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Gagal menyetujui data.');
                    btn.prop('disabled', false).html('<i class="fas fa-check"></i> Appr ' + type.toUpperCase());
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Kesalahan sistem';
                alert('Error: ' + errorMsg);
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Appr ' + type.toUpperCase());
            }
        });
    });

    // Bulk Approval Handler for Selected Rows
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');
        const label = type === 'spv' ? 'SPV QC' : 'QC Inspector';

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menyetujui ' + selectedUuids.length + ' data sebagai ' + label + '?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('wizard.proses1.bulk-approve-selected') }}",
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
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + label);
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
                btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + label);
            }
        });
    });
});
</script>
@endpush

@endsection
