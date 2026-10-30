@extends('layouts.app')
@section('container')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

    .dashboard-panel {
        background: #f8fafc;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 2rem;
    }

    /* Sleek Custom Header */
    .dashboard-header {
        background: linear-gradient(135deg, #090d16 0%, #111827 100%);
        border-radius: 20px;
        padding: 1.5rem 2.5rem;
        box-shadow: 0 20px 40px -15px rgba(17, 24, 39, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.04);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 40%);
        pointer-events: none;
    }

    .dashboard-title-text {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dashboard-subtitle-text {
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .dashboard-time-text {
        font-size: 0.82rem;
        font-weight: 600;
        color: #f1f5f9;
        background: rgba(255, 255, 255, 0.06);
        padding: 8px 16px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    
    .dashboard-time-text:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.15);
    }

    /* Premium Styling for Cards */
    .card {
        background: #ffffff !important;
        border-radius: 20px !important;
        border: 1px solid rgba(226, 232, 240, 0.7) !important;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02), 0 2px 4px -2px rgba(15, 23, 42, 0.01) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.06), 0 10px 10px -5px rgba(15, 23, 42, 0.02) !important;
        border-color: #3b82f6 !important;
    }

    /* Custom Select2 Dark Theme for Dashboard Header */
    .select2-container--bootstrap4 .select2-selection--single {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border: none !important;
        color: #fff !important;
        height: 32px !important;
        border-radius: 8px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #fff !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        line-height: 28px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
        border-color: #fff transparent transparent transparent !important;
    }
    .select2-dropdown {
        background-color: #1a1d23 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
    }
    .select2-results__option {
        color: #cbd5e1 !important;
        font-size: 0.85rem !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: #fff !important;
    }
    .select2-search--dropdown .select2-search__field {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 6px !important;
    }
    
    /* Card Headers */
    .card-header {
        background: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1.5rem 1.5rem 0.5rem 1.5rem !important;
    }
    
    .card-title, h6.fw-bold {
        font-size: 0.95rem !important;
        font-weight: 750 !important;
        color: #0f172a !important;
        letter-spacing: 0.5px !important;
    }

    /* Border end separation styling for sub-charts */
    .border-end {
        border-right: 1px solid #f1f5f9 !important;
    }

    @media (max-width: 991.98px) {
        .border-end {
            border-right: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="content-wrapper">
<div class="dashboard-panel">
    <div class="container-fluid">
        
        <!-- Header Section -->
        <div class="dashboard-header mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="dashboard-title-text d-block">QUALITY CONTROL FURTHER PLANT</span>
                <span class="dashboard-subtitle-text"><i class="fas fa-chart-line me-1"></i> Track key performance indicators and QC activities</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Filter Form -->
                <form method="GET" action="{{ url()->current() }}" class="m-0" id="filterFormMgmt">
                    <div class="d-flex align-items-center">
                        <!-- Product selection filter -->
                        <div class="input-group input-group-sm" style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 2px; margin-right: 15px;">
                            <span class="input-group-text border-0 bg-transparent text-white" style="font-size: 0.8rem;">
                                <i class="fas fa-box"></i>
                            </span>
                            <select name="id_produk" class="form-control form-control-sm border-0 bg-transparent text-white fw-bold shadow-none pe-2 select2-produk" style="font-size: 0.8rem; cursor: pointer; width: 250px;">
                                <option value="">Semua Produk</option>
                                @foreach($produkList as $p)
                                    <option value="{{ $p->id }}" {{ request('id_produk') == $p->id ? 'selected' : '' }} style="color: #000;">{{ $p->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group input-group-sm" style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 2px; margin-right: 15px;">
                            <span class="input-group-text border-0 bg-transparent text-white" style="font-size: 0.8rem;">
                                <i class="fas fa-filter"></i>
                            </span>
                            <select name="filter" class="form-control form-control-sm border-0 bg-transparent text-white fw-bold shadow-none pe-2" style="font-size: 0.8rem; cursor: pointer; width: auto;" onchange="document.getElementById('filterFormMgmt').submit();">
                                <option value="today" {{ (request('filter') == 'today' || !request('filter')) ? 'selected' : '' }} style="color: #000;">Hari Ini</option>
                                <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }} style="color: #000;">Bulan Ini</option>
                                <option value="custom" {{ request('filter') == 'custom' ? 'selected' : '' }} style="color: #000;">Pilih Bulan...</option>
                            </select>
                        </div>
                        @if(request('filter') == 'custom')
                        <div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 2px; margin-right: 15px;">
                            <input type="month" name="month_date" value="{{ request('month_date', date('Y-m')) }}" onchange="document.getElementById('filterFormMgmt').submit();" class="form-control form-control-sm border-0 fw-bold shadow-none" style="background: rgba(255,255,255,0.9); border-radius: 6px; font-size: 0.8rem; color: #000;">
                        </div>
                        @endif
                    </div>
                </form>

                <span class="dashboard-time-text">
                    <i class="far fa-calendar-alt me-2 text-primary"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                </span>
            </div>
        </div>

        <!-- Modern Grid Layout -->
        <div class="row">
            <!-- Unified 3-Column Process Line Compliance (Meatprep, Cooking, Packing & Cartoning) -->
            <div class="col-12">
                @include('dashboard.partials.process-compliance', ['type' => 'management_combined'])
            </div>
            
            <!-- Room Temperature & Chillroom Trend -->
            <div class="col-12">
                @include('dashboard.partials.room-temp-chillroom', ['layout' => 'management'])
            </div>
            
            <!-- Cleaning Area Status -->
            <div class="col-12">
                @include('dashboard.partials.cleaning-area')
            </div>
        </div>

        <div class="row">
            <!-- Column 1: Overview KPI -->
            <div class="col-lg-3 col-md-6 d-flex">
                @include('dashboard.partials.overview-kpis')
            </div>
            
            <!-- Column 2: Contam. Raw Material -->
            <div class="col-lg-3 col-md-6 d-flex">
                @include('dashboard.partials.contam-raw-material')
            </div>

            <!-- Column 3: Contam. Finished Good -->
            <div class="col-lg-3 col-md-6 d-flex">
                @include('dashboard.partials.contam-finished-good')
            </div>

            <!-- Column 4: Non-Conformity & Top Issue -->
            <div class="col-lg-3 col-md-6 d-flex">
                @include('dashboard.partials.non-conformity-issues')
            </div>
        </div>

    </div>
</div>
</div>

@push('scripts')
    <!-- CDN untuk ApexCharts library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk dropdown produk
            $('.select2-produk').select2({
                theme: 'bootstrap4',
                placeholder: 'Semua Produk',
                allowClear: true,
                width: 'resolve'
            });

            // Handle auto-submit saat select2 berubah
            $('.select2-produk').on('change', function() {
                document.getElementById('filterFormMgmt').submit();
            });
        });
    </script>
@endpush

@endsection
