@extends('layouts.app')
@section('container')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

    .main-panel {
        background: #f4f5f7;
        min-height: 100vh;
        font-family: 'DM Sans', sans-serif;
    }

    .content-wrapper {
        padding: 2rem;
    }

    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eaecf0;
    }

    .content-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1d23;
        letter-spacing: -0.3px;
        margin: 0;
    }

    .breadcrumb {
        background: transparent;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8rem;
    }

    .breadcrumb-item a { color: #9aa0ab; text-decoration: none; transition: color 0.2s; }
    .breadcrumb-item a:hover { color: #1a1d23; }
    .breadcrumb-item.active { color: #1a1d23; font-weight: 500; }
    .breadcrumb-item + .breadcrumb-item::before { color: #d1d5db; }

    @media (max-width: 768px) {
        .content-wrapper { padding: 1rem; }
        .content-header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    }

    /* Select2 override agar sesuai form Bootstrap 4 */
    /* ======= Select2 Override — cocok dengan Bootstrap 4 form-control ======= */

    .select2-container { width: 100% !important; }

    .select2-container--bootstrap4 .select2-selection--single {
        height: 38px !important;
        line-height: 1.5 !important;
        padding: 0 2.25rem 0 .75rem !important;
        font-size: .875rem !important;
        font-weight: 400;
        color: #495057 !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: .25rem !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding: 0 !important;
        line-height: normal !important;
        color: #495057 !important;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
        font-style: italic;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        position: absolute !important;
        top: 0 !important;
        right: 0 !important;
        height: 100% !important;
        width: 2.25rem !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        position: absolute !important;
        right: 1.8rem !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 1rem !important;
        line-height: 1 !important;
        color: #adb5bd !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
        color: #dc3545 !important;
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 9999;
    }

    .select2-search--dropdown {
        padding: 6px 8px;
    }
    .select2-search--dropdown .select2-search__field {
        width: 100%;
        border: 1px solid #ced4da !important;
        border-radius: .25rem;
        padding: 5px 8px;
        font-size: 0.8rem;
        outline: none;
        color: #495057;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.15rem rgba(0,123,255,.25);
    }

    .select2-results__option {
        font-size: 0.875rem;
        padding: 6px 12px;
        color: #495057;
    }
    .select2-results__option--highlighted {
        background-color: #3b82f6 !important;
        color: #fff !important;
    }
    .select2-results__option[aria-selected=true] {
        background-color: #e8f0fe !important;
        color: #1a1d23 !important;
    }

    .select2-container .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 32px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        line-height: 14px;
        text-align: center;
        background-color: transparent !important;
        color: #dc3545 !important;
        font-size: 1.3rem;
        font-weight: bold;
        float: none;
        margin-right: 0;
    }
    .select2-container .select2-selection--single .select2-selection__clear:hover {
        color: #a71d2a !important;
        cursor: pointer;
    }

    /* Animasi Custom Loading Logo CPI */
    @keyframes cpi-spin {
        0% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(180deg) scale(1.2); }
        100% { transform: rotate(360deg) scale(1); }
    }

    .card-body.p-0 {
        position: relative;
        overflow: hidden;
    }

    .d-flex > div {
        position: relative;
    }

    .grafana-badge-mask {
        position: absolute;
        top: 0;
        right: 0;
        width: 180px;
        height: 55px;
        background: #ffffff;
        z-index: 10;
        pointer-events: auto;
    }

    /* =====================================================
       PANEL WRAPPER — tinggi konsisten via min-height, bukan
       height tetap, supaya konten Grafana yang bervariasi
       tidak terpotong dan tidak menyisakan whitespace kosong
       yang aneh.
       ===================================================== */
    .grafana-panel-wrap {
        position: relative;
        min-height: 350px;
        height: 350px;
    }
    .grafana-panel-wrap.grafana-panel-short {
        min-height: 320px;
        height: 320px;
    }
    @media (max-width: 768px) {
        .grafana-panel-wrap,
        .grafana-panel-wrap.grafana-panel-short {
            height: 280px;
            min-height: 280px;
        }
    }

    /* =====================================================
       ERROR / RETRY OVERLAY — pengganti pesan error mentah
       bawaan browser ("A network change was detected", dsb)
       saat iframe Grafana gagal dimuat. Karena iframe beda
       origin, kita tidak bisa membaca isi dokumennya secara
       langsung, jadi pendekatannya: beri watchdog timeout +
       tombol retry manual yang selalu tersedia di pojok panel.
       ===================================================== */
    .grafana-error-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: #fafbfc;
        z-index: 98;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1rem;
    }
    .grafana-error-overlay.show { display: flex; }
    .grafana-error-overlay i.icon {
        font-size: 1.8rem;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
    }
    .grafana-error-overlay p {
        font-size: 0.82rem;
        color: #6b7280;
        margin: 0 0 0.75rem 0;
        max-width: 220px;
    }
    .grafana-error-overlay button {
        border: 1px solid #3b4cb8;
        color: #3b4cb8;
        background: #fff;
        border-radius: 6px;
        padding: 5px 14px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .grafana-error-overlay button:hover {
        background: #3b4cb8;
        color: #fff;
    }

    /* Tombol reload manual kecil di pojok kanan atas tiap panel,
       selalu tersedia agar user bisa refresh panel tunggal tanpa
       reload seluruh halaman jika tampilan Grafana macet/berubah. */
    .grafana-refresh-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 15;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.85;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .grafana-refresh-btn:hover {
        opacity: 1;
        transform: rotate(45deg);
        color: #3b4cb8;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item active"> Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            @if(isset($panelIds) && count($panelIds) > 0)
                {{-- Card Filter --}}
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-body p-3">
                        <form id="filterForm" class="row align-items-end">

                            {{-- Pilih Plant --}}
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: #1a1d23;">Pilih Plant</label>
                                <select id="selectPlant" name="var-id_plan" class="form-control" aria-label="Pilih Plant">
                                    @if(Auth::user()->role === 'superadmin' || Auth::user()->id_role == 5)
                                        <option value="$__all">All Plant</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ (isset($idPlan) && $idPlan == $plan->id) ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach($plans as $plan)
                                            @if($plan->id == Auth::user()->id_plan || count($plans) == 1)
                                                <option value="{{ $plan->id }}" selected>{{ $plan->nama_plan }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- Jenis Produk (Select2 Searchable) --}}
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: #1a1d23;">Jenis Produk</label>
                                <select id="selectProduk" name="var-id_produk" class="form-control" aria-label="Pilih Jenis Produk">
                                    <option value="$__all">All Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}" data-plan="{{ $produk->id_plan }}">
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quick Time Range Selector (Fixed: Last 24 hours) --}}
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2" style="display:none;">
                                <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: #1a1d23;">Rentang Waktu (Quick Range)</label>
                                <select id="timeRangeSelect" class="form-control" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                    <option value="from=now-24h&to=now" selected>Last 24 hours</option>
                                </select>
                            </div>

                            {{-- Dari Tanggal --}}
                            <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: #1a1d23;">Dari Tanggal <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="fromDate" class="form-control" aria-label="Dari Tanggal">
                                <div class="invalid-feedback" style="font-size: 0.78rem;">Dari Tanggal wajib diisi.</div>
                            </div>

                            {{-- Sampai Tanggal --}}
                            <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: #1a1d23;">Sampai Tanggal <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="toDate" class="form-control" aria-label="Sampai Tanggal">
                                <div class="invalid-feedback" style="font-size: 0.78rem;">Sampai Tanggal wajib diisi.</div>
                            </div>

                            {{-- Tombol Action: Sanitation & Export Excel --}}
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-2 d-flex align-items-end" style="gap: 8px;">
                                <button type="button" id="btnExportExcel" class="btn btn-success font-weight-bold shadow-sm flex-fill" style="border-radius: 8px; height: 38px; font-size: 0.85rem;" title="Tarik Data Excel">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Sanitation & Verifikasi Peralatan Section --}}
                <div id="sanitationSection" class="card shadow-sm border-0 mb-4" style="border-radius: 14px; background: #ffffff; border: 1.5px solid #c7d2fe !important;">
                    <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border-bottom: 1.5px solid #c7d2fe; border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <div class="d-flex align-items-center">
                            <div class="p-2 mr-3 bg-primary text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-broom fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="m-0 font-weight-bold" style="color: #1e1b4b; letter-spacing: -0.3px;">Sanitation &amp; Verifikasi Peralatan</h5>
                                <small class="text-muted">Perhitungan Real-time Verifikasi Peralatan &amp; Ranking Non-Conformance (NC)</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        {{-- Gambar 1: 3 Summary Cards --}}
                        <div class="row mb-4 justify-content-center">
                            {{-- Total Inspection --}}
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="card border shadow-sm text-center p-3 h-100" style="border-radius: 12px; border-color: #cbd5e1 !important; background: #ffffff;">
                                    <div style="font-size: 0.95rem; font-weight: 600; color: #1e293b;" class="mb-2">Total Inspection</div>
                                    <div id="sanitationTotalInspection" style="font-size: 2.5rem; font-weight: 700; color: #0f172a; line-height: 1.2;">
                                        <i class="fas fa-spinner fa-spin text-muted" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Total NC --}}
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="card border shadow-sm text-center p-3 h-100" style="border-radius: 12px; border-color: #fecaca !important; background: #ffffff;">
                                    <div style="font-size: 0.95rem; font-weight: 600; color: #ef4444;" class="mb-2">Total NC</div>
                                    <div id="sanitationTotalNC" style="font-size: 2.5rem; font-weight: 700; color: #dc2626; line-height: 1.2;">
                                        <i class="fas fa-spinner fa-spin text-muted" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Total OK --}}
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="card border shadow-sm text-center p-3 h-100" style="border-radius: 12px; border-color: #bbf7d0 !important; background: #ffffff;">
                                    <div style="font-size: 0.95rem; font-weight: 600; color: #22c55e;" class="mb-2">Total OK</div>
                                    <div id="sanitationTotalOK" style="font-size: 2.5rem; font-weight: 700; color: #16a34a; line-height: 1.2;">
                                        <i class="fas fa-spinner fa-spin text-muted" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Gambar 2: 3 Top Tables --}}
                        <div class="row">
                            {{-- TOP 3 AREA --}}
                            <div class="col-lg-4 col-md-12 mb-3">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; overflow: hidden; border: 1px solid #c7d2fe !important;">
                                    <div class="px-3 py-2 text-white font-weight-bold d-flex justify-content-between align-items-center" style="background-color: #3b4cb8; font-size: 0.95rem;">
                                        <span>TOP 3 AREA</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped m-0">
                                            <thead style="background-color: #3b4cb8; color: #ffffff;">
                                                <tr>
                                                    <th style="width: 70%; padding-left: 12px; font-weight: 600; font-size: 0.85rem;">Area</th>
                                                    <th style="width: 30%; text-align: right; padding-right: 12px; font-weight: 600; font-size: 0.85rem;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sanitationTopAreaBody">
                                                <tr><td colspan="2" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- TOP 3 BAGIAN / MESIN --}}
                            <div class="col-lg-4 col-md-12 mb-3">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; overflow: hidden; border: 1px solid #c7d2fe !important;">
                                    <div class="px-3 py-2 text-white font-weight-bold d-flex justify-content-between align-items-center" style="background-color: #3b4cb8; font-size: 0.95rem;">
                                        <span>TOP 3 MESIN / PERALATAN</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped m-0">
                                            <thead style="background-color: #3b4cb8; color: #ffffff;">
                                                <tr>
                                                    <th style="width: 70%; padding-left: 12px; font-weight: 600; font-size: 0.85rem;">Mesin / Peralatan</th>
                                                    <th style="width: 30%; text-align: right; padding-right: 12px; font-weight: 600; font-size: 0.85rem;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sanitationTopMesinBody">
                                                <tr><td colspan="2" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- TOP 3 TITIK / PART --}}
                            <div class="col-lg-4 col-md-12 mb-3">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; overflow: hidden; border: 1px solid #c7d2fe !important;">
                                    <div class="px-3 py-2 text-white font-weight-bold d-flex justify-content-between align-items-center" style="background-color: #3b4cb8; font-size: 0.95rem;">
                                        <span>TOP 3 BAGIAN / DETAIL</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped m-0">
                                            <thead style="background-color: #3b4cb8; color: #ffffff;">
                                                <tr>
                                                    <th style="width: 70%; padding-left: 12px; font-weight: 600; font-size: 0.85rem;">Bagian / Detail</th>
                                                    <th style="width: 30%; text-align: right; padding-right: 12px; font-weight: 600; font-size: 0.85rem;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sanitationTopTitikBody">
                                                <tr><td colspan="2" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Detail: Area + Mesin + Bagian + NG Category --}}
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="font-weight-bold m-0" style="color: #1e1b4b; font-size: 0.95rem;">
                                    <i class="fas fa-table mr-2 text-primary"></i>Detail NC Teratas — Plant Further
                                </h6>
                                <small class="text-muted" id="sanitationDetailInfo"></small>
                            </div>
                            <div style="border-radius: 10px; overflow: hidden; border: 1.5px solid #c7d2fe;">
                                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                <table class="table table-sm m-0" id="sanitationDetailTable" style="font-size: 0.84rem; min-width: 720px;">
                                    <thead style="background-color: #3b4cb8; color: #fff; position: sticky; top: 0;">
                                        <tr>
                                            <th style="width: 4%; padding: 8px 10px; font-weight: 600;">#</th>
                                            <th style="width: 21%; padding: 8px 10px; font-weight: 600;">AREA</th>
                                            <th style="width: 21%; padding: 8px 10px; font-weight: 600;">MESIN / PERALATAN</th>
                                            <th style="width: 21%; padding: 8px 10px; font-weight: 600;">BAGIAN / DETAIL</th>
                                            <th style="width: 23%; padding: 8px 10px; font-weight: 600;">NG Category</th>
                                            <th style="width: 10%; padding: 8px 10px; font-weight: 600; text-align: right;">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sanitationDetailBody">
                                        <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            {{-- Pagination --}}
                            <div class="d-flex align-items-center justify-content-end mt-2" id="sanitationDetailPagination" style="gap: 6px;">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Grid iFrame Panel Grafana --}}
                @php
                    $iframeSrc = function($id) use ($idPlan, $activeRole) {
                        return "http://10.68.1.37:3000/d-solo/adkr767/meatprep?orgId=1&panelId={$id}&kiosk=true&theme=light&var-id_plan=" . ($idPlan ?? 1) . "&var-active_role=" . ($activeRole ?? '') . "&from=now-24h&to=now&timezone=browser&var-id_produk=\$__all";
                    };
                    $panelNames = [
                        1  => 'Meatprep',
                        2  => 'Cooking',
                        3  => 'Packing & Cartoning',
                        4  => 'Current Processing',
                        5  => 'Produksi Sebelumnya',
                    ];
                @endphp

                {{-- Row 1: Panel 1, 2, 3 — col-md-4 each --}}
                <div class="row">
                    @foreach([1, 2, 3] as $id)
                        @if(in_array($id, $panelIds))
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #b8e0f7 !important;">
                                <div class="card-header py-2 px-3" style="background: #f0f8ff; border-bottom: 1.5px solid #b8e0f7;">
                                    <span style="font-size: 0.9rem; font-weight: 700; color: #1a6fa8;">
                                        <i class="fas fa-chart-bar mr-2 text-info"></i>
                                        {{ $panelNames[$id] ?? 'Panel ' . $id }}
                                    </span>
                                </div>
                                <div class="card-body p-0 grafana-panel-wrap">
                                    <iframe
                                        id="grafana-panel-{{ $id }}"
                                        data-src="{{ $iframeSrc($id) }}"
                                        width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                        title="{{ $panelNames[$id] ?? 'Panel ' . $id }}">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- Row 2: Panel 4, 5 — col-md-6 each --}}
                <div class="row">
                    @foreach([4, 5] as $id)
                        @if(in_array($id, $panelIds))
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #b8e0f7 !important;">
                                <div class="card-header py-2 px-3" style="background: #f0f8ff; border-bottom: 1.5px solid #b8e0f7;">
                                    <span style="font-size: 0.9rem; font-weight: 700; color: #1a6fa8;">
                                        <i class="fas fa-chart-bar mr-2 text-info"></i>
                                        {{ $panelNames[$id] ?? 'Panel ' . $id }}
                                    </span>
                                </div>
                                <div class="card-body p-0 grafana-panel-wrap">
                                    <iframe
                                        id="grafana-panel-{{ $id }}"
                                        data-src="{{ $iframeSrc($id) }}"
                                        width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                        title="{{ $panelNames[$id] ?? 'Panel ' . $id }}">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- Row 3: Panel 6, 8, 7 — Suhu Ruang, Trend Suhu All Area, Cleaning Area --}}
                @if(count(array_intersect([6, 8, 7], $panelIds)) > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #b8e0f7 !important;">
                            <div class="card-header py-2 px-3" style="background: #f0f8ff; border-bottom: 1.5px solid #b8e0f7;">
                                <span style="font-size: 0.9rem; font-weight: 700; color: #1a6fa8;">
                                    <i class="fas fa-thermometer-half mr-2 text-info"></i>
                                    Room Temperature &amp; Cleaning Area
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap" style="min-height: 320px;">
                                    @foreach([6, 8, 7] as $id)
                                        @if(in_array($id, $panelIds))
                                        <div class="grafana-panel-wrap grafana-panel-short" style="flex: 1 1 260px; min-width: 260px; {{ !$loop->last ? 'border-right: 1px solid #e0edf5;' : '' }}">
                                            <iframe
                                                id="grafana-panel-{{ $id }}"
                                                data-src="{{ $iframeSrc($id) }}"
                                                width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                                title="Room Temperature & Cleaning Area - Panel {{ $id }}">
                                            </iframe>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Row 4: Panel 12, 9, 10 — 1 card besar "Trend Suhu Cooking & Packing" --}}
                @if(count(array_intersect([12, 9, 10,15], $panelIds)) > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #b8e0f7 !important;">
                            <div class="card-header py-2 px-3" style="background: #f0f8ff; border-bottom: 1.5px solid #b8e0f7;">
                                <span style="font-size: 0.9rem; font-weight: 700; color: #1a6fa8;">
                                    <i class="fas fa-fire mr-2 text-warning"></i>
                                    Trend Suhu Cooking, Roasting &amp; Packing
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap" style="min-height: 320px;">
                                    @foreach([12, 9, 10,15] as $id)
                                        @if(in_array($id, $panelIds))
                                        <div class="grafana-panel-wrap grafana-panel-short" style="flex: 1 1 260px; min-width: 260px; {{ !$loop->last ? 'border-right: 1px solid #e0edf5;' : '' }}">
                                            <iframe
                                                id="grafana-panel-{{ $id }}"
                                                data-src="{{ $iframeSrc($id) }}"
                                                width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                                title="Trend Suhu Cooking, Roasting & Packing - Panel {{ $id }}">
                                            </iframe>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Row 6: Panel 13 (Trend Suhu IQF) & Panel 14 (Overview GMP Karyawan) --}}
                <div class="row mb-3">
                    {{-- Panel 13: Trend Suhu IQF --}}
                    @if(in_array(13, $panelIds))
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #b8e0f7 !important;">
                            <div class="card-header py-2 px-3" style="background: #f0f8ff; border-bottom: 1.5px solid #b8e0f7;">
                                <span style="font-size: 0.9rem; font-weight: 700; color: #1a6fa8;">
                                    <i class="fas fa-temperature-low mr-2 text-info"></i>
                                    Trend Suhu IQF
                                </span>
                            </div>
                            <div class="card-body p-0 grafana-panel-wrap">
                                <iframe
                                    id="grafana-panel-13"
                                    data-src="{{ $iframeSrc(13) }}"
                                    width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                    title="Trend Suhu IQF">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Panel 14: Overview GMP Karyawan --}}
                    @if(in_array(14, $panelIds))
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #d4edda !important;">
                            <div class="card-header py-2 px-3" style="background: #f0fff4; border-bottom: 1.5px solid #d4edda;">
                                <span style="font-size: 0.9rem; font-weight: 700; color: #1e7e34;">
                                    <i class="fas fa-user-check mr-2 text-success"></i>
                                    Overview GMP Karyawan
                                </span>
                            </div>
                            <div class="card-body p-0 grafana-panel-wrap">
                                <iframe
                                    id="grafana-panel-14"
                                    data-src="{{ $iframeSrc(14) }}"
                                    width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                    title="Overview GMP Karyawan">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Row 5: Panel 11 — Non-Conformity & Top Issue (sendiri) --}}
                @if(in_array(11, $panelIds))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1.5px solid #f5c6cb !important;">
                            <div class="card-header py-2 px-3" style="background: #fff5f5; border-bottom: 1.5px solid #f5c6cb;">
                                <span style="font-size: 0.9rem; font-weight: 700; color: #c0392b;">
                                    <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>
                                    Non-Conformity &amp; Top Issue
                                </span>
                            </div>
                            <div class="card-body p-0 grafana-panel-wrap grafana-panel-short">
                                <iframe
                                    id="grafana-panel-11"
                                    data-src="{{ $iframeSrc(11) }}"
                                    width="100%" height="100%" frameborder="0" style="border:none; display:block;" loading="lazy" allowfullscreen
                                    title="Non-Conformity & Top Issue">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            @else
                <div class="alert alert-info">Tidak ada panel Grafana yang tersedia untuk ditampilkan.</div>
            @endif
        </div>

    </div>
</div>

@endsection

{{-- =====================================================
     Script di-push ke @stack('scripts') di layout
     agar dijalankan SETELAH jQuery & Select2 siap (line 528-550)
     ===================================================== --}}
@if(isset($panelIds) && count($panelIds) > 0)
@push('scripts')
<script>
(function ($) {
    // Data dari Laravel (di-pass controller)
    var panelIds   = @json($panelIds);
    var activeRole = @json($activeRole ?? '');
    var idPlan     = @json($idPlan ?? 1);
    var allProduks = @json($produks ?? []);

    var IFRAME_TIMEOUT_MS = 10000; // watchdog: kalau iframe stuck > 10 detik, tampilkan overlay retry

    // -----------------------------------------------
    // 1. Inject Custom Loader + Staggered Loading
    //    (Load iframe satu per satu agar tidak berat)
    // -----------------------------------------------
    var logoUrl = '{{ asset(($assetPath ?? "") . "dist/img/cpi-logo.png") }}';
    var $allIframes = $('iframe[id^="grafana-panel-"]');

    // -----------------------------------------------
    // 1b. Pasang mask penutup badge "Powered by Grafana"
    // -----------------------------------------------
    $allIframes.each(function () {
        var $parent = $(this).parent();
        if ($parent.find('.grafana-badge-mask').length === 0) {
            $parent.append('<div class="grafana-badge-mask"></div>');
        }
    });

    // Pasang loader overlay, overlay error/retry, dan tombol refresh manual di setiap iframe
    $allIframes.each(function() {
        var $iframe = $(this);
        var $parent = $iframe.parent();
        $parent.css('position', 'relative');

        var panelId  = $iframe.attr('id');
        var loaderId = 'custom-loader-' + panelId;
        var errorId  = 'custom-error-' + panelId;

        // Loader animasi
        var $loader = $('<div id="' + loaderId + '" style="position:absolute; top:0; left:0; width:100%; height:100%; background:#ffffff; z-index:99; display:flex; justify-content:center; align-items:center;">' +
            '<img src="' + logoUrl + '" style="width: 60px; animation: cpi-spin 2s infinite ease-in-out;">' +
            '</div>');
        $iframe.before($loader);

        // Overlay error + tombol retry (tersembunyi secara default)
        var $errorOverlay = $(
            '<div id="' + errorId + '" class="grafana-error-overlay">' +
                '<i class="fas fa-plug icon"></i>' +
                '<p>Panel gagal dimuat atau koneksi terputus.</p>' +
                '<button type="button" class="btn-retry-panel"><i class="fas fa-redo mr-1"></i>Muat Ulang</button>' +
            '</div>'
        );
        $iframe.before($errorOverlay);

        // Tombol refresh manual kecil, selalu tersedia di pojok panel
        var $refreshBtn = $('<button type="button" class="grafana-refresh-btn" title="Muat ulang panel ini"><i class="fas fa-sync-alt"></i></button>');
        $parent.append($refreshBtn);

        $errorOverlay.find('.btn-retry-panel').on('click', function () {
            reloadSinglePanel(panelId);
        });
        $refreshBtn.on('click', function () {
            reloadSinglePanel(panelId);
        });
    });

    // Reload satu panel saja (dipakai oleh tombol retry / refresh manual)
    function reloadSinglePanel(panelId) {
        var $iframe = $('#' + panelId);
        if (!$iframe.length) return;

        var src = $iframe.data('src');
        var loaderId = 'custom-loader-' + panelId;
        var errorId  = 'custom-error-' + panelId;

        $('#' + errorId).removeClass('show');
        $('#' + loaderId).stop(true).show();

        $iframe.removeAttr('src');
        // Sedikit delay supaya browser benar-benar reset koneksi sebelum load ulang
        setTimeout(function () {
            loadSinglePanel($iframe, src, loaderId, errorId);
        }, 100);
    }

    // Fungsi load 1 iframe dengan watchdog timeout + overlay error
    function loadSinglePanel($iframe, src, loaderId, errorId, onDone) {
        if (!src) {
            if (onDone) onDone();
            return;
        }

        var settled = false;

        $iframe.attr('src', src);

        $iframe.off('load.stagger').on('load.stagger', function () {
            if (settled) return;
            settled = true;
            $iframe.off('load.stagger');
            setTimeout(function () {
                $('#' + loaderId).fadeOut(400);
            }, 800);
            if (onDone) onDone();
        });

        // Watchdog: kalau tidak load dalam batas waktu, tampilkan overlay error + retry
        // (catatan: karena iframe cross-origin, event 'load' tetap bisa terpicu meski
        // Grafana menampilkan halaman error internal — watchdog ini menangani kasus
        // koneksi benar-benar putus/timeout di sisi jaringan.)
        setTimeout(function () {
            if (!settled) {
                $('#' + loaderId).fadeOut(200);
                $('#' + errorId).addClass('show');
                $iframe.off('load.stagger');
                settled = true;
                if (onDone) onDone();
            }
        }, IFRAME_TIMEOUT_MS);
    }

    // Fungsi: load iframe secara berurutan (staggered)
    function loadIframesSequentially(index) {
        if (index >= $allIframes.length) return;

        var $iframe  = $allIframes.eq(index);
        var src      = $iframe.data('src');
        var panelId  = $iframe.attr('id');
        var loaderId = 'custom-loader-' + panelId;
        var errorId  = 'custom-error-' + panelId;

        $('#' + errorId).removeClass('show');

        loadSinglePanel($iframe, src, loaderId, errorId, function () {
            loadIframesSequentially(index + 1);
        });
    }

    // Mulai load dari iframe pertama
    loadIframesSequentially(0);

    // -----------------------------------------------
    // Init Select2 pada #selectProduk
    // -----------------------------------------------
    $('#selectProduk').select2({
        theme: 'bootstrap4',
        placeholder: 'Cari atau pilih produk...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () { return 'Produk tidak ditemukan'; },
            searching: function () { return 'Mencari...'; }
        }
    }).on('change', function () {
        updateGrafanaUrl();
    });

    // -----------------------------------------------
    // Debounce helper — supaya perubahan filter yang cepat
    // (misal user klik-klik tanggal) tidak memicu banyak
    // request AJAX/reload iframe sekaligus.
    // -----------------------------------------------
    function debounce(fn, delay) {
        var timer = null;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () { fn.apply(ctx, args); }, delay);
        };
    }

    var debouncedUpdateGrafanaUrl   = debounce(updateGrafanaUrl, 300);
    var debouncedLoadSanitationData = debounce(loadSanitationData, 300);
    var debouncedLoadDetailPage1    = debounce(function () { loadSanitationDetail(1); }, 300);

    // -----------------------------------------------
    // Perubahan Plant → rebuild opsi Produk
    // -----------------------------------------------
    $('#selectPlant').on('change', function () {
        onPlantChange(true);
    });

    // Time range & custom date → update iframe
    $('#timeRangeSelect').on('change', function () {
        $('#fromDate, #toDate').val('');
        debouncedUpdateGrafanaUrl();
    });
    $('#fromDate, #toDate').on('change', function () {
        debouncedUpdateGrafanaUrl();
    });

    // -----------------------------------------------
    // Filter produk sesuai plant dipilih
    // -----------------------------------------------
    function onPlantChange(triggerUpdate) {
        var selectedPlant  = $('#selectPlant').val();
        var $produk        = $('#selectProduk');
        var currentVal     = $produk.val();
        var isCurrentValid = false;

        $produk.empty().append(new Option('All Produk', '$__all'));

        $.each(allProduks, function (i, p) {
            if (selectedPlant === '$__all' || String(p.id_plan) === String(selectedPlant)) {
                var isSel = String(p.id) === String(currentVal);
                if (isSel) isCurrentValid = true;
                $produk.append(new Option(p.nama_produk, p.id, false, isSel));
            }
        });

        if (!isCurrentValid) {
            $produk.val('$__all');
        }

        $produk.trigger('change.select2');

        if (triggerUpdate) updateGrafanaUrl();
    }

    // -----------------------------------------------
    // Update semua iframe Grafana
    // -----------------------------------------------
    function updateGrafanaUrl() {
        var plant  = $('#selectPlant').val();
        var produk = $('#selectProduk').val() || '$__all';
        var fromVal = $('#fromDate').val();
        var toVal   = $('#toDate').val();

        var timeParam = $('#timeRangeSelect').val();
        if (fromVal && toVal) {
            timeParam = 'from=' + (new Date(fromVal).getTime()) + '&to=' + (new Date(toVal).getTime());
        }

        $.each(panelIds, function (i, id) {
            var $iframe = $('#grafana-panel-' + id);
            if ($iframe.length) {
                $('#custom-error-grafana-panel-' + id).removeClass('show');
                $('#custom-loader-grafana-panel-' + id).stop(true).show();
                $iframe.removeAttr('src');

                var newSrc = 'http://10.68.1.37:3000/d-solo/adkr767/meatprep' +
                    '?orgId=1' +
                    '&panelId=' + id +
                    '&kiosk=true' +
                    '&theme=light' +
                    '&var-id_plan=' + plant +
                    '&var-active_role=' + activeRole +
                    '&' + timeParam +
                    '&timezone=browser' +
                    '&var-id_produk=' + produk;

                $iframe.data('src', newSrc);
            }
        });

        $allIframes = $('iframe[id^="grafana-panel-"]');
        loadIframesSequentially(0);
    }

    // -----------------------------------------------
    // Inisialisasi awal: filter produk sesuai plant aktif
    // -----------------------------------------------
    onPlantChange(false);

    // -----------------------------------------------
    // Handler Event Tombol Export Excel
    // -----------------------------------------------
    $('#btnExportExcel').on('click', function () {
        var plant = $('#selectPlant').val() || '';
        var produk = $('#selectProduk').val() || '';
        var fromVal = $('#fromDate').val() || '';
        var toVal = $('#toDate').val() || '';

        // Reset state validasi sebelumnya
        $('#fromDate').removeClass('is-invalid');
        $('#toDate').removeClass('is-invalid');

        var isValid = true;

        if (!fromVal) {
            $('#fromDate').addClass('is-invalid');
            isValid = false;
        }
        if (!toVal) {
            $('#toDate').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            return; // Hentikan proses export
        }

        var exportUrl = "{{ route('grafana.export-excel') }}" +
            "?id_plan=" + encodeURIComponent(plant) +
            "&id_produk=" + encodeURIComponent(produk) +
            "&from_date=" + encodeURIComponent(fromVal) +
            "&to_date=" + encodeURIComponent(toVal);

        window.location.href = exportUrl;
    });

    // Hapus state invalid saat user mulai mengisi field tanggal
    $('#fromDate, #toDate').on('change input', function () {
        $(this).removeClass('is-invalid');
    });

    // -----------------------------------------------
    // Sanitation Dashboard: Fetch & Render
    // -----------------------------------------------
    var sanitationApiUrl = "{{ route('grafana.sanitation-data') }}";

    function formatNumber(n) {
        if (n === null || n === undefined) return '0';
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function renderTopTable(tbodyId, data, emptyLabel) {
        var $tbody = $('#' + tbodyId);
        $tbody.empty();
        if (!data || data.length === 0) {
            $tbody.append(
                '<tr><td colspan="2" class="text-center py-3 text-muted" style="font-size:0.85rem;">' +
                '<i class="fas fa-inbox mr-1"></i> ' + emptyLabel + '</td></tr>'
            );
            return;
        }
        $.each(data, function (i, row) {
            var rank = i + 1;
            var rankBadge = '';
            if (rank === 1) rankBadge = '<span class="badge badge-danger mr-2" style="border-radius:50%;padding:4px 7px;">' + rank + '</span>';
            else if (rank === 2) rankBadge = '<span class="badge badge-warning mr-2" style="border-radius:50%;padding:4px 7px;">' + rank + '</span>';
            else rankBadge = '<span class="badge badge-secondary mr-2" style="border-radius:50%;padding:4px 7px;">' + rank + '</span>';

            var borderStyle = rank === 1
                ? 'border-left: 4px solid #ef4444;'
                : rank === 2
                    ? 'border-left: 4px solid #f59e0b;'
                    : 'border-left: 4px solid #64748b;';

            $tbody.append(
                '<tr style="' + borderStyle + '">' +
                '<td style="padding-left: 10px; font-size: 0.85rem; vertical-align: middle;">' +
                rankBadge + row.nama +
                '</td>' +
                '<td style="text-align: right; padding-right: 12px; font-weight: 700; font-size: 0.9rem; color: #dc2626; vertical-align: middle;">' +
                formatNumber(row.total) +
                '</td>' +
                '</tr>'
            );
        });
    }

    function loadSanitationData() {
        var plant   = $('#selectPlant').val() || '';
        var fromVal = $('#fromDate').val() || '';
        var toVal   = $('#toDate').val() || '';

        $('#sanitationTotalInspection, #sanitationTotalNC, #sanitationTotalOK').html(
            '<i class="fas fa-spinner fa-spin text-muted" style="font-size:1.5rem;"></i>'
        );
        $('#sanitationTopAreaBody, #sanitationTopMesinBody, #sanitationTopTitikBody').html(
            '<tr><td colspan="2" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>'
        );

        $.ajax({
            url: sanitationApiUrl,
            method: 'GET',
            data: {
                id_plan:   plant,
                from_date: fromVal,
                to_date:   toVal
            },
            success: function (res) {
                $('#sanitationTotalInspection').text(formatNumber(res.total_inspection));
                $('#sanitationTotalNC').text(formatNumber(res.total_nc));
                $('#sanitationTotalOK').text(formatNumber(res.total_ok));

                renderTopTable('sanitationTopAreaBody',  res.top_area,  'Belum ada data NC area');
                renderTopTable('sanitationTopMesinBody', res.top_mesin, 'Belum ada data NC mesin');
                renderTopTable('sanitationTopTitikBody', res.top_titik, 'Belum ada data NC titik/part');
            },
            error: function () {
                $('#sanitationTotalInspection, #sanitationTotalNC, #sanitationTotalOK').html(
                    '<span style="font-size:1.2rem; color:#9ca3af;">—</span>'
                );
                ['sanitationTopAreaBody','sanitationTopMesinBody','sanitationTopTitikBody'].forEach(function(id) {
                    $('#' + id).html(
                        '<tr><td colspan="2" class="text-center py-3 text-danger" style="font-size:0.85rem;">' +
                        '<i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat data. Coba refresh halaman.' +
                        '</td></tr>'
                    );
                });
            }
        });
    }

    // Load saat halaman pertama kali dibuka
    loadSanitationData();

    // Reload sanitation data setiap kali filter plant / tanggal berubah (debounced)
    $('#selectPlant').on('change', function () { debouncedLoadSanitationData(); });
    $('#fromDate, #toDate').on('change', function () { debouncedLoadSanitationData(); });

    // -----------------------------------------------
    // Sanitation Detail Table: Fetch & Render + Pagination
    // -----------------------------------------------
    var sanitationDetailUrl = "{{ route('grafana.sanitation-detail') }}";
    var detailCurrentPage   = 1;
    var detailTotalRows     = 0;
    var detailPerPage       = 10;

    function loadSanitationDetail(page) {
        page = page || 1;
        detailCurrentPage = page;

        var plant   = $('#selectPlant').val() || '';
        var fromVal = $('#fromDate').val() || '';
        var toVal   = $('#toDate').val() || '';

        $('#sanitationDetailBody').html(
            '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>'
        );
        $('#sanitationDetailPagination').empty();
        $('#sanitationDetailInfo').text('');

        $.ajax({
            url: sanitationDetailUrl,
            method: 'GET',
            data: { id_plan: plant, from_date: fromVal, to_date: toVal, page: page },
            success: function (res) {
                detailTotalRows = res.total;
                detailPerPage   = res.per_page;

                var $tbody = $('#sanitationDetailBody');
                $tbody.empty();

                if (!res.data || res.data.length === 0) {
                    $tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-inbox mr-1"></i> Tidak ada data NC.</td></tr>');
                    $('#sanitationDetailInfo').text('');
                    return;
                }

                var offset = (page - 1) * detailPerPage;
                $.each(res.data, function (i, row) {
                    var rowNum = offset + i + 1;
                    var bgStyle = (i % 2 === 0) ? '' : 'background-color: #f8f9ff;';
                    $tbody.append(
                        '<tr style="' + bgStyle + '">' +
                        '<td style="padding: 7px 10px; color: #6b7280;">' + rowNum + '.</td>' +
                        '<td style="padding: 7px 10px; font-weight: 500;">' + (row.area || '-') + '</td>' +
                        '<td style="padding: 7px 10px;">' + (row.mesin || '-') + '</td>' +
                        '<td style="padding: 7px 10px;">' + (row.bagian || '-') + '</td>' +
                        '<td style="padding: 7px 10px;">' +
                            '<span class="badge" style="background-color:#ede9fe; color:#5b21b6; font-weight:500; padding:4px 8px; border-radius:6px; font-size:0.8rem;">' +
                            (row.ng_category || '-') +
                            '</span>' +
                        '</td>' +
                        '<td style="padding: 7px 10px; text-align: right; font-weight: 700; color: #dc2626;">' + formatNumber(row.total) + '</td>' +
                        '</tr>'
                    );
                });

                var from = offset + 1;
                var to   = Math.min(offset + res.data.length, detailTotalRows);
                $('#sanitationDetailInfo').text(from + ' - ' + to + ' / ' + formatNumber(detailTotalRows));

                renderDetailPagination();
            },
            error: function () {
                $('#sanitationDetailBody').html(
                    '<tr><td colspan="6" class="text-center py-4 text-danger">' +
                    '<i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat data. Coba refresh halaman.' +
                    '</td></tr>'
                );
            }
        });
    }

    function renderDetailPagination() {
        var $pg = $('#sanitationDetailPagination');
        $pg.empty();

        var totalPages = Math.ceil(detailTotalRows / detailPerPage);
        if (totalPages <= 1) return;

        $pg.append(
            '<button class="btn btn-sm btn-outline-primary px-2 py-1" id="pgPrev" ' +
            (detailCurrentPage <= 1 ? 'disabled' : '') +
            ' style="border-radius:6px; font-size:0.82rem;" aria-label="Halaman sebelumnya">' +
            '<i class="fas fa-chevron-left"></i></button>'
        );

        var startPage = Math.max(1, detailCurrentPage - 2);
        var endPage   = Math.min(totalPages, detailCurrentPage + 2);

        if (startPage > 1) {
            $pg.append('<span style="font-size:0.82rem; color:#94a3b8; padding:0 4px;">…</span>');
        }
        for (var p = startPage; p <= endPage; p++) {
            var isActive = (p === detailCurrentPage);
            $pg.append(
                '<button class="btn btn-sm pg-num px-2 py-1" data-page="' + p + '" ' +
                'style="border-radius:6px; font-size:0.82rem; min-width:30px; ' +
                (isActive ? 'background-color:#3b4cb8; color:#fff; border-color:#3b4cb8;' : 'border: 1px solid #d1d5db; color:#374151;') +
                '" aria-current="' + (isActive ? 'page' : 'false') + '">' + p + '</button>'
            );
        }
        if (endPage < totalPages) {
            $pg.append('<span style="font-size:0.82rem; color:#94a3b8; padding:0 4px;">…</span>');
        }

        $pg.append(
            '<button class="btn btn-sm btn-outline-primary px-2 py-1" id="pgNext" ' +
            (detailCurrentPage >= totalPages ? 'disabled' : '') +
            ' style="border-radius:6px; font-size:0.82rem;" aria-label="Halaman berikutnya">' +
            '<i class="fas fa-chevron-right"></i></button>'
        );

        $pg.find('#pgPrev').on('click', function () {
            if (detailCurrentPage > 1) loadSanitationDetail(detailCurrentPage - 1);
        });
        $pg.find('#pgNext').on('click', function () {
            if (detailCurrentPage < totalPages) loadSanitationDetail(detailCurrentPage + 1);
        });
        $pg.find('.pg-num').on('click', function () {
            loadSanitationDetail(parseInt($(this).data('page')));
        });
    }

    // Load detail saat halaman pertama kali dibuka
    loadSanitationDetail(1);

    // Reload detail setiap kali filter berubah (reset ke page 1, debounced)
    $('#selectPlant').on('change', function () { debouncedLoadDetailPage1(); });
    $('#fromDate, #toDate').on('change', function () { debouncedLoadDetailPage1(); });

})(jQuery);
</script>
@endpush
@endif