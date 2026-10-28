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

    /* Header */
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

    .breadcrumb-item a {
        color: #9aa0ab;
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb-item a:hover {
        color: #1a1d23;
    }

    .breadcrumb-item.active {
        color: #1a1d23;
        font-weight: 500;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: #d1d5db;
    }

    /* Stat Cards */
    .info-box {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #eaecf0;
        box-shadow: none;
        display: flex;
        align-items: center;
        transition: box-shadow 0.25s ease, transform 0.25s ease;
        animation: slideUp 0.4s ease both;
        margin-bottom: 0;
    }

    .info-box:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        transform: translateY(-2px);
    }

    .col-12:nth-child(1) .info-box { animation-delay: 0.05s; }
    .col-12:nth-child(2) .info-box { animation-delay: 0.10s; }
    .col-12:nth-child(3) .info-box { animation-delay: 0.15s; }
    .col-12:nth-child(4) .info-box { animation-delay: 0.20s; }

    .info-box-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        margin-right: 1.2rem;
        box-shadow: none !important;
    }

    .info-box-icon.bg-info    { background: #eff6ff !important; color: #3b82f6 !important; }
    .info-box-icon.bg-danger  { background: #fff1f2 !important; color: #f43f5e !important; }
    .info-box-icon.bg-success { background: #f0fdf4 !important; color: #22c55e !important; }
    .info-box-icon.bg-warning { background: #fffbeb !important; color: #f59e0b !important; }

    .info-box-content {
        flex: 1;
    }

    .info-box-text {
        font-size: 0.75rem;
        font-weight: 500;
        color: #9aa0ab;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        display: block;
        margin-bottom: 0.4rem;
    }

    .info-box-number {
        font-family: 'DM Mono', monospace;
        font-size: 1.8rem;
        font-weight: 500;
        color: #1a1d23;
        display: block;
        line-height: 1;
    }

    .info-box-number small {
        font-size: 1rem;
        color: #9aa0ab;
        font-weight: 400;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .content-wrapper { padding: 1rem; }
        .content-header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
        .info-box-number { font-size: 1.5rem; }
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
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">

                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-tachometer-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Coming Soon</span>
                            <span class="info-box-number">10<small>%</small></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-heart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Coming Soon</span>
                            <span class="info-box-number">41,410</span>
                        </div>
                    </div>
                </div>

                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Coming Soon</span>
                            <span class="info-box-number">760</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Coming Soon</span>
                            <span class="info-box-number">2,000</span>
                        </div>
                    </div>
                </div>

            </div>
        </div><!--/. container-fluid -->

    </div><!-- /.content-wrapper -->
</div><!-- /.main-panel -->

@endsection