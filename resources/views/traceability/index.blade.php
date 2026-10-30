@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-bold text-primary">
                        <i class="fas fa-search-location mr-2 text-info"></i> Traceability Batch & Produk
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/grafana-dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Traceability</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-body">
                    <!-- Search Form Header -->
                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="text-bold text-dark mb-1">Pencarian Lintas Modul</h5>
                            <p class="text-muted small mb-0">Masukkan Kode Produksi, Nama Bahan/RM, atau Kombinasi Kata Kunci.</p>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('traceability.search') }}" method="GET">
                                <div class="input-group shadow-sm border rounded">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 text-muted pl-3"><i class="fas fa-barcode"></i></span>
                                    </div>
                                    <input type="text" name="production_code" value="{{ $production_code ?? '' }}" 
                                           placeholder="Contoh: LOT-12345, ASIMO, atau Chicken..." 
                                           class="form-control border-0" required minlength="2">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                            <i class="fas fa-search mr-1"></i> Cari
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(isset($results))
                        @php
                            $resultsCollection = collect($results);
                            $totalFormsCount = $resultsCollection->sum('total_count');
                            $moduleOptions = $resultsCollection->map(fn($g) => ['value' => $g['module'], 'label' => $g['label']])->unique('value')->values();
                            $shiftOptions = $resultsCollection->flatMap(fn($g) => collect($g['forms'])->pluck('shift'))->filter()->unique()->sort()->values();
                        @endphp

                        @if($totalFormsCount > 0)
                            {{-- ========== SUMMARY STATS PANEL ========== --}}
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="trace-stat-card">
                                        <div class="trace-stat-icon bg-primary-light">
                                            <i class="fas fa-cubes text-primary"></i>
                                        </div>
                                        <div class="trace-stat-body">
                                            <div class="trace-stat-value">{{ $summary['modules_with_data'] }} <span class="trace-stat-of">/ {{ $summary['total_modules'] }}</span></div>
                                            <div class="trace-stat-label">Modul Terdampak</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="trace-stat-card">
                                        <div class="trace-stat-icon bg-success-light">
                                            <i class="fas fa-file-alt text-success"></i>
                                        </div>
                                        <div class="trace-stat-body">
                                            <div class="trace-stat-value">{{ number_format($summary['total_forms']) }}</div>
                                            <div class="trace-stat-label">Total Form Ditemukan</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="trace-stat-card">
                                        <div class="trace-stat-icon bg-info-light">
                                            <i class="fas fa-calendar-week text-info"></i>
                                        </div>
                                        <div class="trace-stat-body">
                                            <div class="trace-stat-value trace-stat-date">
                                                @if($summary['date_from'] && $summary['date_to'])
                                                    {{ \Carbon\Carbon::parse($summary['date_from'])->translatedFormat('d M Y') }}
                                                    <span class="trace-stat-arrow">→</span>
                                                    {{ \Carbon\Carbon::parse($summary['date_to'])->translatedFormat('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="trace-stat-label">Rentang Tanggal</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ========== PROCESS FLOW BAR ========== --}}
                            @if(isset($process_flow))
                            <div class="trace-flow-wrapper mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-project-diagram text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">Alur Proses Produksi</span>
                                    <span class="text-muted small ml-2">— klik modul untuk langsung ke data</span>
                                </div>
                                <div class="trace-flow-scroll">
                                    <div class="trace-flow-bar">
                                        @foreach($process_flow as $idx => $flow)
                                            @if($idx > 0)
                                                <div class="trace-flow-connector {{ $flow['has_data'] ? 'active' : '' }}">
                                                    <i class="fas fa-chevron-right"></i>
                                                </div>
                                            @endif
                                            <div class="trace-flow-node {{ $flow['has_data'] ? 'active' : 'inactive' }}"
                                                 @if($flow['has_data']) data-target-module="{{ $flow['module'] }}" @endif
                                                 title="{{ $flow['label'] }}{{ $flow['has_data'] ? ' — '.$flow['total'].' data' : '' }}">
                                                <div class="trace-flow-dot">
                                                    @if($flow['has_data'])
                                                        <i class="fas fa-check"></i>
                                                    @endif
                                                </div>
                                                <div class="trace-flow-label">
                                                    {{ Str::limit(preg_replace('/\[.*?\]\s*/', '', $flow['label']), 18) }}
                                                </div>
                                                @if($flow['has_data'])
                                                    <div class="trace-flow-count">{{ $flow['total'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Filter Panel -->
                            <div class="filter-panel mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-filter text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">Filter Hasil</span>
                                </div>
                                <p class="text-muted small mb-3">Gunakan filter untuk mempersempit hasil traceability.</p>

                                <div class="row">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label class="small text-muted mb-1">Modul</label>
                                        <select id="filterModule" class="form-control">
                                            <option value="">Semua Modul</option>
                                            @foreach($moduleOptions as $mod)
                                                <option value="{{ $mod['value'] }}">{{ $mod['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label class="small text-muted mb-1">Shift</label>
                                        <select id="filterShift" class="form-control">
                                            <option value="">Semua Shift</option>
                                            @foreach($shiftOptions as $sh)
                                                <option value="{{ $sh }}">Shift {{ $sh }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted mb-1">Urutan</label>
                                        <select id="filterSort" class="form-control">
                                            <option value="desc">Terbaru &rarr; Terlama</option>
                                            <option value="asc">Terlama &rarr; Terbaru</option>
                                        </select>
                                    </div>
                                </div>

                                <hr class="my-3">
                                <div class="text-muted small mb-0">
                                    <i class="far fa-clock mr-1"></i>
                                    Menampilkan <span id="visibleCount" class="font-weight-bold text-dark">{{ $totalFormsCount }}</span> dari
                                    <span class="font-weight-bold text-dark">{{ $totalFormsCount }}</span> data
                                </div>
                            </div>

                            <div class="text-muted small mb-3">
                                <i class="fas fa-th-list mr-1"></i> Hasil dikelompokkan berdasarkan modul QC
                            </div>
                        @endif
                    @endif

                    <!-- Search Results Section -->
                    @if(isset($results))
                        @forelse($results as $group)
                            <div class="mb-5 trace-group" data-module="{{ $group['module'] }}" id="trace-group-{{ $group['module'] }}">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
                                    <div class="d-flex align-items-center mb-2 mb-md-0">
                                        <h5 class="font-weight-bold text-uppercase text-secondary mb-0 mr-3" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-folder-open text-primary mr-2"></i> {{ $group['label'] }}
                                        </h5>
                                        <span class="badge badge-group-count px-3 py-1 font-weight-bold trace-group-count" style="font-size: 0.75rem;">
                                            {{ $group['total_count'] }} data
                                        </span>
                                    </div>
                                    {{-- Pagination info --}}
                                    @if($group['last_page'] > 1)
                                        <div class="trace-pagination d-flex align-items-center" data-module="{{ $group['module'] }}">
                                            <button type="button" class="btn btn-sm btn-outline-secondary trace-page-btn" data-dir="prev" disabled>
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="mx-2 small text-muted font-weight-bold trace-page-info">
                                                Hal <span class="trace-page-current">1</span> / <span class="trace-page-last">{{ $group['last_page'] }}</span>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary trace-page-btn" data-dir="next">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="row align-items-start trace-group-row">
                                    @foreach($group['forms'] as $form)
                                        <div class="col-md-6 col-lg-4 mb-4 trace-card-col" data-date="{{ $form['date'] }}" data-shift="{{ $form['shift'] }}">
                                            <div class="card trace-card shadow-sm"
                                                 data-module="{{ $group['module'] }}"
                                                 data-form-key="{{ $form['form_key'] }}">

                                                <!-- Card Header -->
                                                <div class="p-3 trace-card-header d-flex align-items-start justify-content-between flex-wrap">
                                                    <div class="mb-2 mb-sm-0">
                                                        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                                            <i class="far fa-calendar-alt text-primary mr-1"></i>
                                                            {{ $form['date'] ? \Carbon\Carbon::parse($form['date'])->translatedFormat('d F Y') : '-' }}
                                                        </div>
                                                        @if($form['shift'])
                                                            <div class="text-muted small mt-1">
                                                                <i class="far fa-clock mr-1"></i> Shift {{ $form['shift'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center trace-actions">
                                                        @if(!empty($form['module_url']) && $form['module_url'] !== '#')
                                                            <a href="{{ $form['module_url'] }}" target="_blank" class="btn btn-sm btn-primary font-weight-bold" title="Buka Halaman Modul">
                                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                            </a>
                                                        @endif
                                                        @if($form['pdf_url'] && $form['pdf_url'] !== '#')
                                                            <form action="{{ $form['pdf_url'] }}" method="POST" target="_blank" class="d-inline ml-1">
                                                                @csrf
                                                                <input type="hidden" name="tanggal_mulai" value="{{ $form['date'] ?? '' }}">
                                                                <input type="hidden" name="tanggal_akhir" value="{{ $form['date'] ?? '' }}">
                                                                <input type="hidden" name="shift_id" value="{{ $form['shift_id'] ?? '' }}">
                                                                <input type="hidden" name="kode_form" value="QF-01/00">
                                                                <input type="hidden" name="uuid" value="{{ $form['form_key'] ?? '' }}">
                                                                <input type="hidden" name="id" value="{{ $form['form_key'] ?? '' }}">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Cetak PDF Form">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Card Body (auto-loaded, selalu terbuka) -->
                                                <div class="trace-body border-top px-3 pb-3 pt-2">
                                                    <div class="trace-loading text-center text-muted py-3">
                                                        <i class="fas fa-spinner fa-spin mr-1"></i> Memuat detail data...
                                                    </div>
                                                    <div class="trace-content d-none"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5 class="text-secondary font-weight-bold">Tidak ada data ditemukan</h5>
                                <p class="text-muted">Tidak ditemukan rekaman form untuk kata kunci "<strong>{{ $production_code }}</strong>".</p>
                            </div>
                        @endforelse
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search-location fa-4x text-primary mb-3" style="opacity: 0.3;"></i>
                            <h5 class="text-secondary font-weight-bold">Silakan masukkan kode batch atau produksi</h5>
                            <p class="text-muted">Sistem akan menelusuri seluruh data form QC secara instan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Data Terkait (Related Modal) -->
<div class="modal fade" id="modalRelated" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title font-weight-bold" id="relatedModalTitle">Detail Terkait</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="relatedModalBody">
                <!-- Content generated via JS -->
            </div>
        </div>
    </div>
</div>

<style>
    /* ========== SUMMARY STAT CARDS ========== */
    .trace-stat-card {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        transition: all 0.2s ease;
    }
    .trace-stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }
    .trace-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .bg-primary-light { background-color: #dbeafe; }
    .bg-success-light { background-color: #dcfce7; }
    .bg-info-light    { background-color: #e0f2fe; }
    .trace-stat-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }
    .trace-stat-of {
        font-size: 0.9rem;
        font-weight: 600;
        color: #94a3b8;
    }
    .trace-stat-date {
        font-size: 0.85rem !important;
        font-weight: 700 !important;
    }
    .trace-stat-arrow {
        color: #94a3b8;
        margin: 0 0.25rem;
    }
    .trace-stat-label {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 2px;
    }

    /* ========== PROCESS FLOW BAR ========== */
    .trace-flow-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.25rem;
    }
    .trace-flow-scroll {
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    .trace-flow-scroll::-webkit-scrollbar {
        height: 4px;
    }
    .trace-flow-scroll::-webkit-scrollbar-track {
        background: #e2e8f0;
        border-radius: 4px;
    }
    .trace-flow-scroll::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }
    .trace-flow-bar {
        display: flex;
        align-items: flex-start;
        min-width: max-content;
        padding: 0.5rem 0;
    }
    .trace-flow-node {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 80px;
        max-width: 100px;
        cursor: default;
        position: relative;
    }
    .trace-flow-node.active {
        cursor: pointer;
    }
    .trace-flow-node.active:hover .trace-flow-dot {
        transform: scale(1.15);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }
    .trace-flow-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        transition: all 0.2s ease;
        margin-bottom: 6px;
    }
    .trace-flow-node.active .trace-flow-dot {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    .trace-flow-node.inactive .trace-flow-dot {
        background: #e2e8f0;
        color: #94a3b8;
    }
    .trace-flow-label {
        font-size: 0.65rem;
        text-align: center;
        line-height: 1.2;
        color: #64748b;
        font-weight: 600;
        word-break: break-word;
    }
    .trace-flow-node.active .trace-flow-label {
        color: #1e40af;
        font-weight: 700;
    }
    .trace-flow-count {
        font-size: 0.6rem;
        background: #2563eb;
        color: #fff;
        padding: 0.05rem 0.4rem;
        border-radius: 50px;
        font-weight: 700;
        margin-top: 2px;
    }
    .trace-flow-connector {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 2px;
        margin-top: 6px;
        color: #cbd5e1;
        font-size: 0.55rem;
    }
    .trace-flow-connector.active {
        color: #2563eb;
    }

    /* ========== PAGINATION CONTROLS ========== */
    .trace-pagination .trace-page-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }
    .trace-pagination .trace-page-btn:not(:disabled):hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .trace-pagination .trace-page-info {
        min-width: 70px;
        text-align: center;
    }

    /* ========== Filter Panel ========== */
    .filter-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
    }

    /* Group badge "N data" — pil gelap, sesuai referensi desain */
    .badge-group-count {
        background-color: #3b4b8c;
        color: #fff;
        border-radius: 50px;
    }

    /* Card traceability: aksen biru di kiri, konsisten dengan referensi */
    .trace-card {
        border-radius: 10px;
        border: 1px solid #e9ecef;
        border-left: 4px solid #2563eb;
        transition: all 0.2s ease;
    }
    .trace-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.08) !important;
    }

    /* Baris field dalam card */
    .border-bottom-dashed {
        border-bottom: 1px dashed #dee2e6 !important;
    }
    .trace-fields:first-child {
        margin-top: 0 !important;
    }

    /* Nilai field yang cocok dengan kata kunci pencarian, di-highlight */
    .trace-highlight {
        background-color: #fef3c7;
        color: #92400e;
        padding: 0.05rem 0.5rem;
        border-radius: 4px;
        font-weight: 700;
    }

    /* Badge status (RELEASE / HOLD / dll), warna otomatis sesuai nilai */
    .trace-status {
        display: inline-block;
        padding: 0.15rem 0.65rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.7rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .trace-status-success { background-color: #dcfce7; color: #15803d; }
    .trace-status-warning { background-color: #fef3c7; color: #b45309; }
    .trace-status-danger  { background-color: #fee2e2; color: #b91c1c; }
    .trace-status-neutral { background-color: #e2e8f0; color: #475569; }

    /* Utility yang dipakai di halaman ini tapi tidak tersedia bawaan Bootstrap 4/AdminLTE */
    .btn-xs {
        padding: 0.15rem 0.5rem;
        font-size: 0.7rem;
        line-height: 1.4;
        border-radius: 0.2rem;
    }
    .gap-1 { gap: 0.25rem; }

    /* Pagination loading state */
    .trace-group-row.is-loading {
        opacity: 0.4;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    const searchTerm = @json($production_code ?? '');
    const detailsCache = {};
    const modulePageUrl = @json(route('traceability.modulePage'));

    // ===== Process Flow Bar: klik node → scroll ke grup modul =====
    $(document).on('click', '.trace-flow-node.active', function() {
        const targetModule = $(this).data('target-module');
        if (!targetModule) return;
        const $target = $('#trace-group-' + targetModule);
        if ($target.length) {
            $('html, body').animate({
                scrollTop: $target.offset().top - 100
            }, 400);
            // Flash highlight
            $target.addClass('bg-light');
            setTimeout(() => $target.removeClass('bg-light'), 1500);
        }
    });

    // ===== Auto-load detail per card (tanpa klik), pakai IntersectionObserver =====
    // supaya tidak menembak ratusan AJAX sekaligus saat hasil pencarian banyak.
    function fetchAndRender(card) {
        const loading = card.find('.trace-loading');
        const content = card.find('.trace-content');
        const moduleKey = card.data('module');
        const formKey = card.data('form-key');
        const cacheKey = moduleKey + '::' + formKey;

        if (detailsCache[cacheKey]) {
            renderDetails(content, detailsCache[cacheKey]);
            loading.addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('traceability.formDetails') }}",
            method: "GET",
            data: { module: moduleKey, form_key: formKey, search: searchTerm }
        }).done(function(response) {
            detailsCache[cacheKey] = response.details;
            renderDetails(content, response.details);
        }).fail(function() {
            content.html('<p class="text-danger small mb-0 py-2"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat data detail.</p>');
            content.removeClass('d-none');
        }).always(function() {
            loading.addClass('d-none');
        });
    }

    function observeCards() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries, obs) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        fetchAndRender($(entry.target));
                        obs.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '150px 0px' });

            document.querySelectorAll('.trace-card:not(.observed)').forEach(function(el) {
                el.classList.add('observed');
                observer.observe(el);
            });
        } else {
            // Fallback browser lama: load semua langsung
            $('.trace-card:not(.observed)').each(function() {
                $(this).addClass('observed');
                fetchAndRender($(this));
            });
        }
    }

    observeCards();

    // Label field yang dianggap sebagai "status" dan diberi pill warna
    const statusLabelPattern = /status/i;

    function statusBadgeClass(value) {
        const v = (value ?? '').toString().trim().toUpperCase();
        if (['RELEASE', 'APPROVED', 'LOLOS', 'LULUS', 'OK', 'PASS', 'SESUAI'].includes(v)) return 'trace-status-success';
        if (['HOLD', 'PENDING', 'REVIEW', 'MENUNGGU'].includes(v)) return 'trace-status-warning';
        if (['REJECT', 'REJECTED', 'GAGAL', 'FAILED', 'TOLAK'].includes(v)) return 'trace-status-danger';
        if (v === '' || v === '-') return null;
        return 'trace-status-neutral';
    }

    function fieldValueHtml(label, value) {
        if (statusLabelPattern.test(label)) {
            const cls = statusBadgeClass(value);
            if (cls) {
                return `<span class="trace-status ${cls}">${value}</span>`;
            }
        }

        const strVal = (value === null || value === undefined || value === '') ? '-' : String(value);
        const term = (searchTerm || '').toString().trim().toLowerCase();
        if (term && strVal.toLowerCase().includes(term)) {
            return `<span class="trace-highlight">${strVal}</span>`;
        }
        return strVal;
    }

    function renderDetails(container, details) {
        container.empty();

        if (!details || details.length === 0) {
            container.html('<p class="text-muted small mb-0 py-2">Tidak ada detail tambahan.</p>');
            container.removeClass('d-none');
            return;
        }

        details.forEach((detail, index) => {
            const wrap = $('<div class="trace-fields"></div>');
            if (index > 0) wrap.addClass('pt-2 mt-2 border-top');

            let fieldsHtml = '';
            for (const [label, value] of Object.entries(detail.fields)) {
                fieldsHtml += `
                    <div class="d-flex justify-content-between small py-1 border-bottom-dashed">
                        <span class="text-muted">${label}</span>
                        <span class="font-weight-bold text-dark text-right ml-2">${fieldValueHtml(label, value)}</span>
                    </div>
                `;
            }

            let relatedHtml = '';
            if (detail.related && detail.related.length > 0) {
                relatedHtml = '<div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1">';
                detail.related.forEach(r => {
                    const encodedLabel = encodeURIComponent(JSON.stringify(r.label));
                    const encodedFields = encodeURIComponent(JSON.stringify(r.fields));
                    relatedHtml += `
                        <button type="button" class="btn btn-xs btn-outline-info trace-related-btn font-weight-bold mr-1 mb-1"
                                data-label="${encodedLabel}" data-fields="${encodedFields}">
                            <i class="fas fa-link mr-1"></i> ${r.label}
                        </button>
                    `;
                });
                relatedHtml += '</div>';
            }

            wrap.html(fieldsHtml + relatedHtml);
            container.append(wrap);
        });

        container.removeClass('d-none');
    }

    $(document).on('click', '.trace-related-btn', function() {
        const label = JSON.parse(decodeURIComponent($(this).data('label')));
        const fields = JSON.parse(decodeURIComponent($(this).data('fields')));

        $('#relatedModalTitle').text(label);
        const body = $('#relatedModalBody');
        body.empty();

        for (const [key, value] of Object.entries(fields)) {
            body.append(`
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">${key}</span>
                    <span class="font-weight-bold text-dark small">${value ?? '-'}</span>
                </div>
            `);
        }

        $('#modalRelated').modal('show');
    });

    // ===== PAGINATION PER MODUL (AJAX) =====
    $(document).on('click', '.trace-page-btn:not(:disabled)', function() {
        const $btn = $(this);
        const $pagination = $btn.closest('.trace-pagination');
        const moduleKey = $pagination.data('module');
        const dir = $btn.data('dir');
        const $currentSpan = $pagination.find('.trace-page-current');
        const $lastSpan = $pagination.find('.trace-page-last');
        let currentPage = parseInt($currentSpan.text());
        const lastPage = parseInt($lastSpan.text());

        const newPage = dir === 'next' ? currentPage + 1 : currentPage - 1;
        if (newPage < 1 || newPage > lastPage) return;

        const $group = $('#trace-group-' + moduleKey);
        const $row = $group.find('.trace-group-row');

        // Loading state
        $row.addClass('is-loading');
        $pagination.find('.trace-page-btn').prop('disabled', true);

        $.ajax({
            url: modulePageUrl,
            method: 'GET',
            data: {
                module: moduleKey,
                search: searchTerm,
                page: newPage
            }
        }).done(function(response) {
            // Build new cards HTML
            let cardsHtml = '';
            (response.forms || []).forEach(function(form) {
                const dateFormatted = form.date ? new Date(form.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
                const shiftHtml = form.shift ? `<div class="text-muted small mt-1"><i class="far fa-clock mr-1"></i> Shift ${form.shift}</div>` : '';
                
                let actionsHtml = '';
                if (form.module_url && form.module_url !== '#') {
                    actionsHtml += `<a href="${form.module_url}" target="_blank" class="btn btn-sm btn-primary font-weight-bold" title="Buka Halaman Modul"><i class="fas fa-external-link-alt mr-1"></i></a>`;
                }

                cardsHtml += `
                    <div class="col-md-6 col-lg-4 mb-4 trace-card-col" data-date="${form.date || ''}" data-shift="${form.shift || ''}">
                        <div class="card trace-card shadow-sm" data-module="${moduleKey}" data-form-key="${form.form_key}">
                            <div class="p-3 trace-card-header d-flex align-items-start justify-content-between flex-wrap">
                                <div class="mb-2 mb-sm-0">
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        <i class="far fa-calendar-alt text-primary mr-1"></i> ${dateFormatted}
                                    </div>
                                    ${shiftHtml}
                                </div>
                                <div class="d-flex align-items-center trace-actions">
                                    ${actionsHtml}
                                </div>
                            </div>
                            <div class="trace-body border-top px-3 pb-3 pt-2">
                                <div class="trace-loading text-center text-muted py-3">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Memuat detail data...
                                </div>
                                <div class="trace-content d-none"></div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $row.html(cardsHtml);
            $row.removeClass('is-loading');

            // Update pagination state
            $currentSpan.text(response.current_page);
            const $prevBtn = $pagination.find('[data-dir="prev"]');
            const $nextBtn = $pagination.find('[data-dir="next"]');
            $prevBtn.prop('disabled', response.current_page <= 1);
            $nextBtn.prop('disabled', response.current_page >= response.last_page);

            // Re-observe new cards for lazy-load details
            observeCards();

            // Scroll to top of group
            $('html, body').animate({
                scrollTop: $group.offset().top - 80
            }, 300);

        }).fail(function() {
            $row.removeClass('is-loading');
            $pagination.find('.trace-page-btn').prop('disabled', false);
            alert('Gagal memuat halaman. Silakan coba lagi.');
        });
    });

    // ===== Filter & Urutan (client-side, dari data yang sudah ter-render) =====
    const $filterModule = $('#filterModule');
    const $filterShift = $('#filterShift');
    const $filterSort = $('#filterSort');

    function applyFilters() {
        const modVal = $filterModule.val();
        const shiftVal = $filterShift.val();
        const sortVal = $filterSort.val();
        let visibleTotal = 0;

        $('.trace-group').each(function() {
            const $group = $(this);
            const groupModule = $group.data('module');

            if (modVal && String(groupModule) !== String(modVal)) {
                $group.hide();
                return;
            }

            const $row = $group.find('.trace-group-row');
            const $cols = $group.find('.trace-card-col');
            let groupVisible = 0;

            $cols.each(function() {
                const $col = $(this);
                const shiftMatch = !shiftVal || String($col.data('shift')) === String(shiftVal);
                if (shiftMatch) {
                    $col.show();
                    groupVisible++;
                } else {
                    $col.hide();
                }
            });

            // Urutkan ulang card yang tampil berdasarkan tanggal
            const sorted = $cols.filter(':visible').sort(function(a, b) {
                const da = new Date($(a).data('date'));
                const db = new Date($(b).data('date'));
                return sortVal === 'asc' ? da - db : db - da;
            });
            sorted.each(function() {
                $row.append(this);
            });

            $group.toggle(groupVisible > 0);

            visibleTotal += groupVisible;
        });

        $('#visibleCount').text(visibleTotal);
    }

    $filterModule.on('change', applyFilters);
    $filterShift.on('change', applyFilters);
    $filterSort.on('change', applyFilters);
});
</script>
@endpush
@endsection