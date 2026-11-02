<!DOCTYPE html>
<html lang="en">
<head>
    <!-- oke -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Paperless Further</title>
     @php
       $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
    @endphp
    <link rel="stylesheet" href="{{ asset($assetPath . 'plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'dist/DataTables/css/jquery.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPath . 'plugins/datatables-rowgroup/css/rowGroup.bootstrap4.min.css') }}">
    <link rel="icon" type="image/png" href="{{ asset($assetPath . 'dist/img/FotoLogo.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GX63SBZ58T"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-GX63SBZ58T');
    </script>
    <style>
         /* ====== MODERN WHITE & BLUE SIDEBAR STYLING ====== */

        /* Main Sidebar - Clean White Background */
        .main-sidebar,
        aside.main-sidebar,
        .sidebar-dark-primary .main-sidebar,
        .sidebar-dark-primary.main-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.08) !important;
            border-right: 1px solid #e2e8f0 !important;
        }

        /* ====== BRAND LOGO SECTION ====== */
        .brand-link {
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 0.8rem 1.2rem !important;
            height: 57px !important;
            /* width: 100% !important; */
            display: flex !important;
            align-items: center !important;
            background: #ffffff !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .brand-link:hover {
            /* background: #f1f5f9 !important; */
            text-decoration: none !important;
        }

        .brand-text {
            font-weight: 700 !important;
            font-size: 1.05rem !important;
            color: #1e293b !important;
            text-shadow: none !important;
            letter-spacing: 0.3px !important;
            margin-left: 0.5rem !important;
        }

        .brand-image {
            max-height: 33px !important;
            width: auto !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1)) !important;
            transition: all 0.3s ease !important;
        }

        /* ====== USER PANEL SECTION ====== */
        .user-panel {
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 1.2rem 1.5rem !important;
            margin-bottom: 0 !important;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%) !important;
            border-radius: 12px !important;
            margin: 1rem !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
        }

        .user-panel:hover {
            background: linear-gradient(135deg, #e0f2fe 0%, #f8fafc 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1) !important;
        }

        .user-panel .image img {
            border: 3px solid #3b82f6 !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2) !important;
        }

        .user-panel .image img:hover {
            border-color: #2563eb !important;
            transform: scale(1.05) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
        }

        .user-panel .info a,
        .user-panel .info p {
            color: #1e293b !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
        }

        /* ====== NAVIGATION MENU ====== */
        .nav-sidebar {
            padding: 0.5rem 0 !important;
        }

        .nav-item {
            margin-bottom: 0.25rem !important;
        }

        .nav-link {
            padding: 0.85rem 1.2rem !important;
            color: #475569 !important;
            border-radius: 0 25px 25px 0 !important;
            margin: 0 1rem 0 0 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            font-weight: 500 !important;
            border-left: 3px solid transparent !important;
        }

        /* Blue accent line on left */
        .nav-link::before {
            content: '' !important;
            position: absolute !important;
            left: 0 !important;
            top: 50% !important;
            transform: translateY(-50%) scaleY(0) !important;
            height: 60% !important;
            width: 3px !important;
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 0 3px 3px 0 !important;
        }

        .nav-link:hover:not(.active) {
            background: rgba(219, 234, 254, 0.5) !important;
            color: #1e40af !important;
            transform: translateX(5px) !important;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1) !important;
            border-left-color: #93c5fd !important;
        }
        
        .nav-link:hover:not(.active)::before {
            transform: translateY(-50%) scaleY(0.5) !important;
        }
        
        .nav-link.active {
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
            border-left-color: #ffffff !important;
            font-weight: 700 !important;
        }
        
        .nav-link.active::before {
            transform: translateY(-50%) scaleY(1) !important;
            background: #ffffff !important;
        }

        /* ====== ICONS STYLING ====== */
        .nav-icon {
            margin-right: 0.75rem !important;
            width: 20px !important;
            text-align: center !important;
            font-size: 1.1rem !important;
            color: #64748b !important;
            transition: all 0.3s ease !important;
        }

        .nav-link:hover .nav-icon {
            color: #1e40af !important;
            transform: scale(1.15) !important;
        }

        .nav-link.active .nav-icon {
            color: #ffffff !important;
            animation: iconBounce 0.5s ease !important;
        }

        @keyframes iconBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* ====== BADGE STYLING ====== */
        .badge {
            font-size: 0.7rem !important;
            padding: 0.35rem 0.65rem !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
        }

        .badge-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3) !important;
            animation: badgePulse 2s ease-in-out infinite !important;
        }

        @keyframes badgePulse {
            0%, 100% {
                box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 4px 10px rgba(59, 130, 246, 0.5);
                transform: scale(1.05);
            }
        }

        /* ====== TREEVIEW / SUBMENU ====== */
        .nav-treeview {
            padding-left: 0 !important;
            background: linear-gradient(135deg, #f1f5f9 0%, #e0f2fe 100%) !important;
            border-radius: 0 15px 15px 0 !important;
            margin: 0.5rem 1rem 0.5rem 0 !important;
            border-left: 3px solid #3b82f6 !important;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05) !important;
        }

        .nav-treeview .nav-link {
            padding: 0.5rem 0.8rem 0.5rem 2rem !important;
            font-size: 0.825rem !important;
            margin: 0.15rem 0.5rem !important;
            border-radius: 8px !important;
            color: #64748b !important;
            border-left: none !important;
            display: flex !important;
            align-items: center !important;
            white-space: normal !important;
            word-break: break-word !important;
            min-height: 2.3rem;
        }
        
        .nav-treeview .nav-link p {
            margin: 0 !important;
            white-space: normal !important;
            word-break: break-word !important;
            line-height: 1.3 !important;
            width: 100% !important;
        }

        .nav-treeview .nav-link:hover {
            background: linear-gradient(90deg, #bfdbfe 0%, #93c5fd 100%) !important;
            color: #1e40af !important;
            transform: translateX(8px) !important;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.15) !important;
        }

        .nav-treeview .nav-link.active {
            background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.3) !important;
        }

        .nav-treeview .nav-link .nav-icon {
            font-size: 0.65rem !important;
        }

        /* ====== NAV HEADER ====== */
        .nav-header {
            color: #64748b !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 1.2px !important;
            padding: 1.5rem 1.5rem 0.75rem !important;
            border-top: 1px solid #e2e8f0 !important;
            margin-top: 1rem !important;
            position: relative !important;
        }

        .nav-header::after {
            content: '' !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 1.5rem !important;
            width: 40px !important;
            height: 3px !important;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%) !important;
            border-radius: 2px !important;
        }

        /* ====== ANGLE ICONS ====== */
        .fas.fa-angle-left,
        .right {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            color: #94a3b8 !important;
        }

        .nav-link:hover .fas.fa-angle-left,
        .nav-link:hover .right {
            color: #1e40af !important;
            transform: translateX(3px) !important;
        }

        .nav-item.menu-open > .nav-link .fas.fa-angle-left,
        .nav-item.menu-open > .nav-link .right {
            transform: rotate(-90deg) !important;
            color: #3b82f6 !important;
        }

        .nav-link.active .fas.fa-angle-left,
        .nav-link.active .right {
            color: #ffffff !important;
        }

        /* ====== SCROLLBAR STYLING ====== */
        .sidebar::-webkit-scrollbar {
            width: 6px !important;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 10px !important;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #93c5fd 0%, #3b82f6 100%) !important;
            border-radius: 10px !important;
            transition: background 0.3s ease !important;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%) !important;
        }

        /* ====== ENTRANCE ANIMATION ====== */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .nav-item {
            animation: slideInLeft 0.4s ease forwards !important;
        }

        .nav-item:nth-child(1) { animation-delay: 0.05s !important; }
        .nav-item:nth-child(2) { animation-delay: 0.1s !important; }
        .nav-item:nth-child(3) { animation-delay: 0.15s !important; }
        .nav-item:nth-child(4) { animation-delay: 0.2s !important; }
        .nav-item:nth-child(5) { animation-delay: 0.25s !important; }
        .nav-item:nth-child(6) { animation-delay: 0.3s !important; }
        .nav-item:nth-child(7) { animation-delay: 0.35s !important; }
        .nav-item:nth-child(8) { animation-delay: 0.4s !important; }
        .nav-item:nth-child(9) { animation-delay: 0.45s !important; }
        .nav-item:nth-child(10) { animation-delay: 0.5s !important; }

        /* ====== RESPONSIVE DESIGN ====== */
        @media (max-width: 768px) {
            .nav-link {
                margin: 0 !important;
                border-radius: 0 !important;
                padding: 0.75rem 1rem !important;
            }
            
            .nav-treeview {
                margin: 0 !important;
                border-radius: 0 !important;
            }
            
            .user-panel {
                margin: 0.5rem !important;
                padding: 1rem !important;
            }
            
            .brand-link {
                padding: 1rem 1.2rem !important;
            }
        }

        /* ====== DATE INPUT CUSTOM STYLING ====== */
        input[type="date"]#tanggal_expired {
            position: relative !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0.5rem 0.75rem !important;
            transition: all 0.3s ease !important;
        }

        input[type="date"]#tanggal_expired:hover {
            border-color: #3b82f6 !important;
        }

        input[type="date"]#tanggal_expired:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none !important;
        }

        input[type="date"]#tanggal_expired::-webkit-calendar-picker-indicator {
            cursor: pointer !important;
            color: #3b82f6 !important;
            filter: brightness(0) saturate(100%) invert(45%) sepia(99%) saturate(1677%) hue-rotate(201deg) brightness(98%) contrast(93%) !important;
        }

        input[type="date"]#tanggal_expired::-webkit-datetime-edit-fields-wrapper {
            display: flex !important;
        }

        input[type="date"]#tanggal_expired::-webkit-datetime-edit-day-field,
        input[type="date"]#tanggal_expired::-webkit-datetime-edit-month-field,
        input[type="date"]#tanggal_expired::-webkit-datetime-edit-year-field {
            padding: 0 2px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
        }

        /* ====== HOVER GLOW EFFECT ====== */
        .nav-link:hover {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.2) !important;
        }
        
        .nav-link.active {
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4) !important;
        }

        /* ====== FOCUS STATES FOR ACCESSIBILITY ====== */
        .nav-link:focus {
            outline: 2px solid rgba(59, 130, 246, 0.5) !important;
            outline-offset: 2px !important;
        }

        .brand-link:focus {
            outline: 2px solid rgba(59, 130, 246, 0.5) !important;
            outline-offset: -2px !important;
        }

        /* ====== SMOOTH TEXT RENDERING ====== */
        * {
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        /* ====== ADDITIONAL POLISH ====== */
        .nav-sidebar .nav-link p {
            margin-bottom: 0 !important;
            transition: all 0.3s ease !important;
        }

        .nav-sidebar .nav-link:hover p {
            letter-spacing: 0.3px !important;
            font-weight: 600 !important;
        }

        /* Active menu text styling */
        .nav-link.active p {
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        }

        /* Menu open state styling */
        .nav-item.menu-open > .nav-link {
            background: linear-gradient(90deg, #e0f2fe 0%, #dbeafe 100%) !important;
            color: #1e40af !important;
            font-weight: 600 !important;
        }

        /* Nested submenu styling */
        .nav-treeview .nav-treeview {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%) !important;
            margin-left: 1rem !important;
            border-left: 2px solid #60a5fa !important;
        }

        .nav-treeview .nav-treeview .nav-link {
            padding-left: 3.5rem !important;
            font-size: 0.825rem !important;
        }

        /* Arrow icons for nested menus */
        .fa-arrow-circle-down {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            color: #94a3b8 !important;
        }

        .nav-link:hover .fa-arrow-circle-down {
            color: #1e40af !important;
        }

        .menu-open > .nav-link .fa-arrow-circle-down {
            transform: rotate(180deg) !important;
            color: #3b82f6 !important;
        }

        /* Auto-Uppercase Standarization */
        input[type="text"]:not(.no-uppercase):not(.login-wrapper input), 
        textarea:not(.no-uppercase):not(.login-wrapper textarea),
        .select2-selection__rendered {
            text-transform: uppercase;
        }

        /* ====== FIX SIDEBAR COLLAPSE / MINI MODE ====== */
        body.sidebar-collapse .main-sidebar:not(:hover) .nav-link p,
        body.sidebar-collapse .main-sidebar:not(:hover) .brand-text,
        body.sidebar-collapse .main-sidebar:not(:hover) .user-panel .info,
        body.sidebar-collapse .main-sidebar:not(:hover) .right,
        body.sidebar-collapse .main-sidebar:not(:hover) .fas.fa-angle-left {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        body.sidebar-collapse .main-sidebar:hover {
            width: 320px !important;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15) !important;
            z-index: 1050 !important;
        }

        body.sidebar-collapse .main-sidebar:hover .nav-link p,
        body.sidebar-collapse .main-sidebar:hover .brand-text,
        body.sidebar-collapse .main-sidebar:hover .user-panel .info {
            display: inline-block !important;
            width: auto !important;
            height: auto !important;
            visibility: visible !important;
            opacity: 1 !important;
            white-space: normal !important;
            word-break: break-word !important;
        }

        body.sidebar-collapse .main-sidebar:not(:hover) .nav-treeview {
            display: none !important;
        }

        body.sidebar-collapse .main-sidebar:not(:hover) .nav-link {
            padding: 0.85rem 0.5rem !important;
            text-align: center !important;
            margin: 0 !important;
            border-radius: 8px !important;
            transform: none !important;
        }

        body.sidebar-collapse .main-sidebar:not(:hover) .nav-link .nav-icon {
            margin-right: 0 !important;
            width: 100% !important;
        }

        body.sidebar-collapse .main-sidebar:not(:hover) .nav-link::before {
            display: none !important;
        }

        body.sidebar-collapse .brand-link {
            height: 57px !important;
            padding: 0.8rem 0.5rem !important;
            justify-content: center !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        @if (!request()->is('login') && !request()->is('local-login'))
            {{-- Tampilkan sidebar dan navbar --}}
            @include('partials.navbar')
            @include('partials.audit-mode-banner')
        @endif

        @yield('container')
    </div>
    <script src="{{ asset ($assetPath . 'plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset ($assetPath . 'dist/DataTables/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset ($assetPath . 'dist/DataTables/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'dist/js/adminlte.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/datatables-rowgroup/js/dataTables.rowGroup.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/datatables-rowgroup/js/rowGroup.bootstrap4.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'dist/js/moment.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'dist/js/datetime-moment.js') }}"></script>
    <script src="{{ asset ($assetPath . 'dist/js/demo.js') }}"></script>
    <!-- <script src="{{ asset ($assetPath . 'dist/js/load-ajax.js') }}"></script> -->
    <script src="{{ asset ($assetPath . 'plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <script src="{{ asset ($assetPath . 'plugins/chart.js/Chart.min.js') }}"></script>
    <!-- <script src="{{ asset($assetPath . 'dist/js/pages/dashboard2.js') }}?v={{ @filemtime(public_path($assetPath . 'dist/js/pages/dashboard2.js')) }}"></script>

    <script src="{{ asset($assetPath . 'dist/js/pages/dashboard.js') }}?v={{ @filemtime(public_path($assetPath . 'dist/js/pages/dashboard.js')) }}"></script> -->

    <!-- Global Form Submission Handler - Mencegah double submit -->
    <script src="{{ asset($assetPath . 'js/form-submission-handler.js') }}"></script>
@stack('scripts')
<script>
// data table
$(document).ready(function() {
    $('#myTable').DataTable();
    $('#table-box').DataTable();
    $('#bahan-forming').DataTable({
        order: [[1, 'asc'], [2, 'asc'], [3, 'asc']], // Sort by Plan, Produk, Formula
        rowGroup: {
            dataSrc: [1, 2, 3] // Group by Plan, Produk, Formula
        }
    });
     $('#bahan-non-forming').DataTable({
        order: [[1, 'asc'], [2, 'asc'], [3, 'asc']], // Sort by Plan, Produk, Formula
        rowGroup: {
            dataSrc: [1, 2, 3] // Group by Plan, Produk, Formula
        }
    });
    $('#bahan-emulsi').DataTable({
        order: [[1, 'asc'], [2, 'asc'], [3, 'asc'], [4, 'asc'], [5, 'asc']], // Sort by Plan, Produk, Nomor Emulsi, Total Pemakaian, Nama Emulsi
        rowGroup: {
        dataSrc: [1, 2, 3, 4, 5] // Group by Plan, Produk, Nomor Emulsi, Total Pemakaian, Nama Emulsi
    }
    });
});
document.addEventListener('DOMContentLoaded', function () {
  var path = window.location.pathname || '';
  var isBahanFormingCreatePage = path.indexOf('bahan-forming/create') !== -1;
  if (isBahanFormingCreatePage) {
    var $plan = $('#id_plan_select');
    var $produk = $('#id_produk_select');
    var $formula = $('#id_formula_select_1');
    if ($plan.length && $produk.length && $formula.length) {
      $plan.off('change.bahanFormingCreatePlan').on('change.bahanFormingCreatePlan', function () {
        var planId = $(this).val();
        $produk.html('<option value="">Memuat...</option>');
        $formula.html('<option value="">Pilih Formula</option>');
        if (!planId) {
          $produk.html('<option value="">Pilih Produk</option>');
          return;
        }

        $.get('{{ url('qc-sistem/ajax/produk-by-plan') }}/' + encodeURIComponent(planId))
          .done(function (items) {
            var opts = '<option value="">Pilih Produk</option>';
            if (Array.isArray(items)) {
              items.forEach(function (it) {
                opts += '<option value="' + it.id + '">' + it.nama_produk + '</option>';
              });
            }
            $produk.html(opts);
          })
          .fail(function () {
            $produk.html('<option value="">Gagal memuat data</option>');
          });
      });

      $produk.off('change.bahanFormingCreateProduk').on('change.bahanFormingCreateProduk', function () {
        var produkId = $(this).val();
        $formula.html('<option value="">Memuat...</option>');
        if (!produkId) {
          $formula.html('<option value="">Pilih Formula</option>');
          return;
        }

        $.get('{{ url('qc-sistem/get-formula-by-produk') }}/' + encodeURIComponent(produkId))
          .done(function (items) {
            var opts = '<option value="">Pilih Formula</option>';
            if (Array.isArray(items)) {
              items.forEach(function (it) {
                opts += '<option value="' + it.id + '">' + it.nomor_formula + '</option>';
              });
            }
            $formula.html(opts);
          })
          .fail(function () {
            $formula.html('<option value="">Gagal memuat data</option>');
          });
      });

      if ($plan.val()) {
        $plan.trigger('change');
      }
    }
  }

  // Script ini khusus untuk halaman Persiapan Bahan Forming.
  // Banyak form lain (mis. Suhu Adonan) memakai id yang sama: #id_plan_select dan #id_produk_select.
  // Jika tidak dibatasi, halaman lain bisa ikut ter-redirect ke persiapan bahan.
  
  var isPersiapanBahanFormingPage = path.indexOf('persiapan-bahan-forming') !== -1;
  if (!isPersiapanBahanFormingPage) {
    return;
  }

  var $produk = $('#id_produk_select');
  var $formula = $('#id_formula_select');
  var $suhu = $('#id_suhu_adonan_select');
  var $tbody = $('#tabel-bahan-forming tbody');

  // Helper URL via Blade (aman base path)
  var URL_NOMOR_FORMULA_BY_PRODUK = '/paperless_futher/qc-sistem/get-formula-by-produk';
  var URL_BAHAN_BY_FORMULA = '/paperless_futher/qc-sistem/ajax/bahan-forming-by-formula';
  var URL_SUHU_BY_PRODUK = '/paperless_futher/qc-sistem/get-suhu-adonan-by-produk';
  var URL_NON_FORMING_FORMULA_BY_PRODUK = '/paperless_futher/qc-sistem/ajax/nomor-formula-non-forming-by-produk';

  // Debug: pastikan element ada
  console.log('Produk select found:', $('#id_produk_select').length);
  console.log('Formula select found:', $('#id_formula_select').length);
  console.log('Suhu select found:', $('#id_suhu_adonan_select').length);

  // Event delegation untuk produk dropdown
  $(document).on('change', '#id_produk_select', function () {
    var id_produk = $(this).val();
    console.log('Produk changed to:', id_produk);

    // Sinkronkan query string id_produk agar URL selalu sesuai pilihan produk
    try {
      var url = new URL(window.location.href);
      if (id_produk) {
        url.searchParams.set('id_produk', id_produk);
      } else {
        url.searchParams.delete('id_produk');
      }
      window.history.replaceState({}, '', url.toString());
    } catch (e) {}

    var selectedOption = this.selectedOptions ? this.selectedOptions[0] : this.options[this.selectedIndex];
    var rawStatusBahan = (selectedOption && selectedOption.getAttribute('data-status-bahan')) ? String(selectedOption.getAttribute('data-status-bahan')).trim().toLowerCase() : '';
    var normalizedStatus = rawStatusBahan.replace(/[\s_]+/g, '-');
    var isNonForming = normalizedStatus === 'non-forming' || normalizedStatus.indexOf('non') === 0;

    if (id_produk && isNonForming) {
      window.location.href = "{{ route('persiapan-bahan-non-forming.create') }}" + '?id_produk=' + encodeURIComponent(id_produk);
      return;
    }
    
    var $formula = $('#id_formula_select');
    var $suhu = $('#id_suhu_adonan_select');
    var $tbody = $('#tabel-bahan-forming tbody');
    
    $formula.html('<option value="">Memuat...</option>');
    $suhu.html('<option value="">Memuat...</option>');
    $tbody.html('<tr><td colspan="9" class="text-center">Silakan pilih formula.</td></tr>');

    if (!id_produk) {
      $formula.html('<option value="">Pilih Nomor Formula</option>');
      $suhu.html('<option value="">Pilih Suhu Adonan</option>');
      return;
    }

    // Fallback: jika status_bahan tidak terisi benar, cek master non-forming berdasarkan id_produk
    var nfUrl = URL_NON_FORMING_FORMULA_BY_PRODUK + '/' + id_produk;
    $.get(nfUrl)
      .done(function (items) {
        if (Array.isArray(items) && items.length > 0) {
          window.location.href = "{{ route('persiapan-bahan-non-forming.create') }}" + '?id_produk=' + encodeURIComponent(id_produk);
        }
      })
      .fail(function () {});

    // Load nomor formula berdasarkan produk
    var formulaUrl = URL_NOMOR_FORMULA_BY_PRODUK + '/' + id_produk;
    console.log('Loading formula from:', formulaUrl);
    
    $.get(formulaUrl)
      .done(function (data) {
        console.log('[PBF] formula by produk response:', data);
        var opts = '<option value="">Pilih Nomor Formula</option>';
        if (data && Array.isArray(data)) {
          data.forEach(function (it) {
            opts += '<option value="' + it.id + '">' + it.nomor_formula + '</option>';
          });
        }
        $formula.html(opts);
      })
      .fail(function (xhr) {
        console.error('[PBF] formula load failed:', xhr.status, xhr.responseText);
        $formula.html('<option value="">Gagal memuat data</option>');
      });

    // Load suhu adonan berdasarkan produk (langsung setelah produk dipilih)
    var suhuUrl = URL_SUHU_BY_PRODUK + '/' + id_produk;
    console.log('Loading suhu from:', suhuUrl);
    
    $.get(suhuUrl)
      .done(function (items) {
        console.log('[PBF] suhu std by produk response:', items);
        var opts = '<option value="">Pilih Suhu Adonan</option>';
        if (Array.isArray(items) && items.length) {
          items.forEach(function (sa) {
            opts += '<option value="' + sa.id + '">STD: ' + sa.std_suhu + '</option>';
          });
        } else {
          opts = '<option value="">Tidak ada data Suhu Adonan</option>';
        }
        $suhu.html(opts);
      })
      .fail(function (xhr) {
        console.error('[PBF] suhu std load failed:', xhr.status, xhr.responseText);
        $suhu.html('<option value="">Gagal memuat data</option>');
      });
  });

  // Dynamic row deletion for forming ingredients table
  $(document).on('click', '.hapus-baris-bahan', function () {
    $(this).closest('tr').remove();
    reindexBahanFormingTable();
  });

  function reindexBahanFormingTable() {
    // Re-index for create.blade.php table
    $('#tabel-bahan-forming tbody tr').each(function(index) {
      $(this).find('td:first').text(index + 1);
    });
    // Re-index for edit.blade.php table
    $('#tabel-bahan-forming-body tr').each(function(index) {
      $(this).find('td:first .badge').text(index + 1);
    });
  }

  // Event delegation untuk formula dropdown persiapan bahan forming
  $(document).on('change', '#id_formula_select', function () {
    var id_formula = $(this).val();
    console.log('Formula changed to:', id_formula);
    
    var $tbody = $('#tabel-bahan-forming tbody');
    
    if (!id_formula) {
      $tbody.html('<tr><td colspan="9" class="text-center">Silakan pilih formula.</td></tr>');
      return;
    }

    // Load Bahan by Formula
    $tbody.html('<tr><td colspan="9" class="text-center">Memuat data...</td></tr>');
    var bahanUrl = URL_BAHAN_BY_FORMULA + '/' + id_formula;
    console.log('Loading bahan from:', bahanUrl);
    
    $.get(bahanUrl)
      .done(function (data) {
        console.log('[PBF] bahan by formula response:', data);
        if (Array.isArray(data) && data.length) {
          var rows = '';
          data.forEach(function (item, idx) {
            rows += '<tr>'
              + '<td>' + (idx + 1) + '</td>'
              + '<td>' + (item.formula ? item.formula.nomor_formula : '-') + '</td>'
              + '<td>' + item.nama_rm + '</td>'
              + '<td>' + item.berat_rm + '</td>'
              + '<td>'
              + '<input type="text" name="kode_produksi_bahan[]" value="" class="form-control" placeholder="Kode Produksi Bahan">'
              + '</td>'
              + '<td>'
                + '<input type="hidden" name="id_bahan_forming[]" value="' + item.id + '">'
                + '<input type="text" name="suhu[]" value="" class="form-control" placeholder="Suhu">'
              + '</td>'
              + '<td>'
                + '<select name="sensori[]" class="form-control">'
                  + '<option value="OK">OK</option>'
                  + '<option value="Tidak OK">Tidak OK</option>'
                + '</select>'
              + '</td>'
              + '<td>'
                + '<input type="text" name="keterangan_bahan[]" value="" class="form-control" placeholder="Keterangan">'
              + '</td>'
              + '<td class="text-center">'
                + '<button type="button" class="btn btn-danger btn-sm hapus-baris-bahan" title="Hapus Bahan">'
                  + '<i class="fas fa-trash"></i>'
                + '</button>'
              + '</td>'
              + '</tr>';
          });
          $tbody.html(rows);
        } else {
          $tbody.html('<tr><td colspan="9" class="text-center">Tidak ada data bahan forming untuk formula ini.</td></tr>');
        }
      })
      .fail(function (xhr) {
        console.error('[PBF] bahan by formula failed:', xhr.status, xhr.responseText);
        $tbody.html('<tr><td colspan="9" class="text-center">Gagal memuat data.</td></tr>');
      });
  });

});
// AJAX SEARCH untuk Persiapan Bahan Forming
$(document).ready(function() {
    let searchTimeout;
    let currentPage = 1;
    let currentSearch = '{{ $search ?? "" }}';
    let currentPerPage = {{ $perPage ?? 5 }};

    // Search input handler
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const searchValue = $(this).val().trim();
        currentSearch = searchValue;
        currentPage = 1;
        
        // Show/hide clear button
        if (searchValue.length > 0) {
            $('#clearBtn').show();
        } else {
            $('#clearBtn').hide();
        }
        
        searchTimeout = setTimeout(function() {
            performSearch();
        }, 300);
    });

    // Clear search
    $('#clearBtn').on('click', function() {
        $('#searchInput').val('');
        $(this).hide();
        currentSearch = '';
        currentPage = 1;
        performSearch();
    });

    // Per page change
    $('#perPageSelect').on('change', function() {
        currentPerPage = $(this).val();
        currentPage = 1;
        performSearch();
    });

    // Pagination click handler
    $(document).on('click', '#paginationContainer .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            const urlParams = new URLSearchParams(url.split('?')[1]);
            const page = urlParams.get('page');
            if (page) {
                currentPage = parseInt(page);
                performSearch();
            }
        }
    });

    function performSearch() {
        // Show loading
        $('#tableBody').html(`
            <tr>
                <td colspan="12" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Sedang mencari data...</p>
                </td>
            </tr>
        `);

        $.ajax({
            url: '{{ route("persiapan-bahan-forming.search") }}',
            method: 'GET',
            data: {
                search: currentSearch,
                page: currentPage,
                per_page: currentPerPage
            },
            success: function(response) {
                updateTable(response);
                updatePagination(response.pagination);
                updateDataInfo(response);
            },
            error: function(xhr) {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="12" class="text-center py-4">
                            <div class="text-danger">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h5>Terjadi Kesalahan</h5>
                                <p>Gagal memuat data. Silakan coba lagi.</p>
                                <button class="btn btn-primary" onclick="location.reload()">
                                    <i class="fas fa-refresh"></i> Muat Ulang
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    function updateTable(response) {
        let tableHtml = '';
        
        if (response.data.length === 0) {
            tableHtml = `
                <tr>
                    <td colspan="12" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-4x text-muted opacity-50 mb-4"></i>
                            ${currentSearch ? `
                                <h5 class="text-muted">Tidak ada data yang ditemukan</h5>
                                <p class="text-muted mb-4">Tidak ada data yang cocok dengan pencarian "<strong>${currentSearch}</strong>".</p>
                                <button class="btn btn-outline-secondary mr-2" onclick="$('#searchInput').val(''); $('#clearBtn').click();">
                                    <i class="fas fa-times mr-1"></i> Hapus Filter
                                </button>
                            ` : `
                                <h5 class="text-muted">Belum ada data</h5>
                                <p class="text-muted mb-4">Silakan tambah data persiapan bahan forming terlebih dahulu.</p>
                            `}
                            @if(auth()->user()?->hasPermissionTo('create-persiapan-bahan-forming'))
                            <a href="{{ route('persiapan-bahan-forming.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i> Tambah Data
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
            `;
        } else {
            response.data.forEach((item, index) => {
                const rowNumber = (currentPage - 1) * currentPerPage + index + 1;
                const tanggal = (item.tanggal ?? '-');
                const jenisBadge = item.jenis === 'Forming'
                    ? '<span class="badge bg-primary">Forming</span>'
                    : '<span class="badge bg-secondary">Non Forming</span>';

                const shiftBadge = item.shift_id == 1
                    ? '<span class="badge bg-primary">Shift ' + ((item.shift && item.shift.shift) ? item.shift.shift : 'Shift 1') + '</span>'
                    : item.shift_id == 2
                        ? '<span class="badge bg-success">Shift ' + ((item.shift && item.shift.shift) ? item.shift.shift : 'Shift 2') + '</span>'
                        : '<span class="badge bg-secondary">Shift ' + ((item.shift && item.shift.shift) ? item.shift.shift : item.shift_id) + '</span>';

                const catatan = item.catatan ? (item.catatan.length > 50 ? item.catatan.substring(0, 50) + '...' : item.catatan) : '-';
                const dibuatOleh = item.dibuat_oleh || '-';

                let aksiHtml = '<span class="text-muted">-</span>';
                if (item.jenis === 'Forming') {
                    aksiHtml = `
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-forming/${item.uuid}" class="btn btn-primary btn-sm" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()?->hasPermissionTo('edit-persiapan-bahan-forming'))
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-forming/${item.uuid}/edit" class="btn btn-warning btn-sm" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-forming-export-pdf/${item.uuid}" class="btn btn-danger btn-sm" title="Cetak PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @if(auth()->user()?->hasPermissionTo('view-persiapan-bahan-forming'))
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-forming/${item.uuid}/logs" class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                            <i class="fas fa-history"></i>
                        </a>
                        @endif
                        @if(auth()->user()?->hasPermissionTo('delete-persiapan-bahan-forming'))
                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-uuid="${item.uuid}" title="Hapus Data">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    `;
                } else if (item.jenis === 'Non Forming') {
                    aksiHtml = `
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-non-forming/${item.uuid}" class="btn btn-primary btn-sm" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()?->hasPermissionTo('edit-persiapan-bahan-non-forming'))
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-non-forming/${item.uuid}/edit" class="btn btn-warning btn-sm" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        <a href="/paperless_futher/qc-sistem/persiapan-bahan-non-forming-export-pdf/${item.uuid}" class="btn btn-danger btn-sm" title="Cetak PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    `;
                }

                tableHtml += `
                    <tr>
                        <td class="text-center align-middle">${rowNumber}</td>
                        <td class="text-center align-middle">${jenisBadge}</td>
                        <td class="align-middle">${shiftBadge}</td>
                        <td class="text-center align-middle"><span class="badge badge-secondary">${tanggal}</span></td>
                        <td class="text-center align-middle"><span>${item.jam ? item.jam : '-'}</span></td>
                   <td class="align-middle">${item.nama_produk || '-'} ${item.berat_sampling ? item.berat_sampling + ' gram' : ''}</td> 
                        <td class="align-middle">${item.kode_produksi || '-'}</td>
                        <td class="align-middle">${item.nomor_formula || '-'}</td>
                        <td class="text-center align-middle">${item.kondisi || '-'}</td>
                        <td class="align-middle"><small class="text-muted">${catatan}</small></td>
                        <td class="align-middle">${dibuatOleh}</td>
                        <td class="text-center align-middle">${aksiHtml}</td>
                    </tr>
                `;
            });
        }
        
        $('#tableBody').html(tableHtml);
    }

    function generateApprovalButtons(item) {
        const userRole = {{ auth()->user()->id_role ?? 'null' }};
        let buttonsHtml = '';
        
        // Debug: log approval data
        console.log('Approval data for item:', item.uuid, {
            approved_by_qc: item.approved_by_qc,
            approved_by_produksi: item.approved_by_produksi,
            approved_by_spv: item.approved_by_spv
        });

        if ([1, 5].includes(userRole)) {
            // Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_qc ? 'btn-success' : 'btn-outline-success'} approve-btn" 
                        data-id="${item.uuid}" 
                        data-type="qc"
                        title="Disetujui oleh QC"
                        ${item.approved_by_qc ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_qc ? 'fa-check-circle' : 'fa-check'}"></i> QC
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_produksi ? 'btn-primary' : 'btn-secondary'}" 
                        title="${item.approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi'}"
                        disabled>
                    <i class="fas ${item.approved_by_produksi ? 'fa-check-circle' : 'fa-clock'}"></i> FM/FL PRODUKSI
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_spv ? 'btn-dark' : 'btn-secondary'}" 
                        title="${item.approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV'}"
                        disabled>
                    <i class="fas ${item.approved_by_spv ? 'fa-check-circle' : 'fa-clock'}"></i> SPV
                </button>
            `;
        } else if (userRole === 2) {
            // Role 2: Hanya tampilkan tombol Produksi
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_produksi ? 'btn-primary' : (item.approved_by_qc ? 'btn-outline-primary' : 'btn-secondary')} ${item.approved_by_qc && !item.approved_by_produksi ? 'approve-btn' : ''}" 
                        data-id="${item.uuid}" 
                        data-type="produksi"
                        title="${!item.approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : (item.approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi')}"
                        ${!item.approved_by_qc || item.approved_by_produksi ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_produksi ? 'fa-check-circle' : (!item.approved_by_qc ? 'fa-clock' : 'fa-check')}"></i> Disetujui oleh Produksi
                </button>
            `;
        } else if (userRole === 3) {
            // Role 3: Hanya tampilkan tombol QC
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_qc ? 'btn-success' : 'btn-outline-success'} approve-btn" 
                        data-id="${item.uuid}" 
                        data-type="qc"
                        title="Disetujui oleh QC"
                        ${item.approved_by_qc ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_qc ? 'fa-check-circle' : 'fa-check'}"></i> Disetujui oleh QC
                </button>
            `;
        } else if (userRole === 4) {
            // Role 4: Hanya tampilkan tombol SPV
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_spv ? 'btn-dark' : (item.approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary')} ${item.approved_by_produksi && !item.approved_by_spv ? 'approve-btn' : ''}" 
                        data-id="${item.uuid}" 
                        data-type="spv"
                        title="${!item.approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : (item.approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV')}"
                        ${!item.approved_by_produksi || item.approved_by_spv ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_spv ? 'fa-check-circle' : (!item.approved_by_produksi ? 'fa-clock' : 'fa-check')}"></i> Disetujui oleh SPV
                </button>
            `;
        } else {
            // Role lain: Tampilkan semua tombol sebagai read-only
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_qc ? 'btn-success' : 'btn-secondary'}" 
                        title="${item.approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC'}"
                        disabled>
                    <i class="fas ${item.approved_by_qc ? 'fa-check-circle' : 'fa-clock'}"></i> QC
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_produksi ? 'btn-primary' : 'btn-secondary'}" 
                        title="${item.approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi'}"
                        disabled>
                    <i class="fas ${item.approved_by_produksi ? 'fa-check-circle' : 'fa-clock'}"></i> Produksi
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_spv ? 'btn-dark' : 'btn-secondary'}" 
                        title="${item.approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV'}"
                        disabled>
                    <i class="fas ${item.approved_by_spv ? 'fa-check-circle' : 'fa-clock'}"></i> SPV
                </button>
            `;
        }

        return buttonsHtml;
    }

    function generateApprovalStatus(item) {
        let statusHtml = '';
        
        if (item.approved_by_qc) {
            statusHtml += '<small class="badge badge-success d-block mb-1">✓ QC</small>';
        }
        if (item.approved_by_produksi) {
            statusHtml += '<small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>';
        }
        if (item.approved_by_spv) {
            statusHtml += '<small class="badge badge-dark d-block mb-1">✓ SPV</small>';
        }
        
        return statusHtml;
    }

    function updatePagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            $('#paginationContainer').html('');
            return;
        }

        let paginationHtml = `
            <div class="row mt-3">
                <div class="col-md-6">
                    <small class="">
                        Menampilkan ${pagination.from} sampai ${pagination.to} 
                        dari ${pagination.total} data
                    </small>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm justify-content-center mb-0">
        `;

        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="?page=${pagination.current_page - 1}">&laquo;</a></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHtml += `<li class="page-item active">
                    <span class="page-link" style="background-color: #007bff; border-color: #007bff; color: white;">${i}</span>
                </li>`;
            } else {
                paginationHtml += `<li class="page-item">
                    <a class="page-link" href="?page=${i}" style="color: #007bff;">${i}</a>
                </li>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="?page=${pagination.current_page + 1}">&raquo;</a></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
        }

        paginationHtml += `
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        `;

        $('#paginationContainer').html(paginationHtml);
    }

    function updateDataInfo(response) {
        let infoText = `Menampilkan ${response.pagination.from} sampai ${response.pagination.to} dari ${response.pagination.total} data`;
        if (currentSearch) {
            infoText += ` <span class="badge badge-warning ml-2"><i class="fas fa-filter mr-1"></i>Filter: "${currentSearch}"</span>`;
        }
        $('#dataInfo').html(infoText);
    }

    // Show clear button if there's initial search value
    if (currentSearch) {
        $('#clearBtn').show();
    }
});

// Delete button functionality using event delegation
$(document).on('click', '.delete-btn', function(e) {
    e.preventDefault();
    const uuid = $(this).data('uuid');
    
    if (confirm('Yakin ingin menghapus data ini?')) {
        // Create a temporary form for delete
        const form = $('<form>', {
            'method': 'POST',
            'action': `/paperless_futher/qc-sistem/persiapan-bahan-forming/${uuid}`
        });
        
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': csrfToken
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_method',
            'value': 'DELETE'
        }));
        
        // Append form to body and submit
        $('body').append(form);
        form.submit();
    }
});

// Approval button functionality using event delegation
$(document).on('click', '.approve-btn', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    const type = $(this).data('type');
    const button = $(this);
    
    // Konfirmasi sebelum approve
    const typeNames = {
        'qc': 'QC',
        'produksi': 'Produksi', 
        'spv': 'SPV'
    };
    
    if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
        // Disable button sementara dan show loading
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        // AJAX request untuk approval
        // Determine the correct route based on current page
        let approveUrl;
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('input-metal-detector')) {
            approveUrl = `/paperless_futher/qc-sistem/input-metal-detector/${id}/approve`;
        } else if (currentPath.includes('persiapan-bahan-forming')) {
            approveUrl = `/paperless_futher/qc-sistem/persiapan-bahan-forming/${id}/approve`;
        } else if (currentPath.includes('persiapan-bahan-emulsi')) {
            approveUrl = `/paperless_futher/qc-sistem/persiapan-bahan-emulsi/${id}/approve`;
        } else if (currentPath.includes('persiapan-bahan-better')) {
            approveUrl = `/paperless_futher/qc-sistem/persiapan-bahan-better/${id}/approve`;
        } else if (currentPath.includes('ketidaksesuaian-plastik')) {
            approveUrl = `/paperless_futher/qc-sistem/ketidaksesuaian-plastik/${id}/approve`;
        } else if (currentPath.includes('ketidaksesuaian-benda-asing')) {
            approveUrl = `/paperless_futher/qc-sistem/ketidaksesuaian-benda-asing/${id}/approve`;
        } else if (currentPath.includes('dokumentasi')) {
            approveUrl = `/paperless_futher/qc-sistem/dokumentasi/${id}/approve`;
        } else if (currentPath.includes('chillroom')) {
            approveUrl = `/paperless_futher/qc-sistem/chillroom/${id}/approve`;
        } else if (currentPath.includes('seasoning')) {
            approveUrl = `/paperless_futher/qc-sistem/seasoning/${id}/approve`;
        } else if (currentPath.includes('shoestring')) {
            approveUrl = `/paperless_futher/qc-sistem/shoestring/${id}/approve`;
        } else if (currentPath.includes('rebox')) {
            approveUrl = `/paperless_futher/qc-sistem/rebox/${id}/approve`;
        } else if (currentPath.includes('produk-forming')) {
            approveUrl = `/paperless_futher/qc-sistem/produk-forming/${id}/approve`;
        } else if (currentPath.includes('produk-non-forming')) {
            approveUrl = `/paperless_futher/qc-sistem/produk-non-forming/${id}/approve`;
        }else if (currentPath.includes('produk-yum')) {
            approveUrl = `/paperless_futher/qc-sistem/produk-yum/${id}/approve`;
        } 
        else if (currentPath.includes('timbangan')) {
            approveUrl = `/paperless_futher/qc-sistem/timbangan/${id}/approve`;
        } 
          else if (currentPath.includes('thermometer')) {
            approveUrl = `/paperless_futher/qc-sistem/thermometer/${id}/approve`;
        } 
        else {
            // Default fallback
            approveUrl = `/paperless_futher/qc-sistem/persiapan-bahan-forming/${id}/approve`;
        }
        
        $.ajax({
            url: approveUrl,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                type: type
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                          .addClass('btn-success')
                          .html('<i class="fas fa-check-circle"></i> Approved');
                    
                    // Reload halaman setelah delay singkat
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Gagal menyetujui data: ' + response.message);
                    button.prop('disabled', false);
                    // Restore original button text
                    const originalText = {
                        'qc': '<i class="fas fa-check"></i> QC',
                        'produksi': '<i class="fas fa-check"></i> Fm/Fl PRODUKSI',
                        'spv': '<i class="fas fa-check"></i> SPV'
                    };
                    button.html(originalText[type]);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyetujui data';
                console.log(xhr);
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                button.prop('disabled', false);
                // Restore original button text
                const originalText = {
                    'qc': '<i class="fas fa-check"></i> QC',
                    'produksi': '<i class="fas fa-check"></i>FM/FL PRODUKSI',
                    'spv': '<i class="fas fa-check"></i> SPV'
                };
                button.html(originalText[type]);
            }
        });
    }
});

// AJAX SEARCH untuk Persiapan Bahan Emulsi
$(document).ready(function() {
    let searchTimeoutEmulsi;
    let currentPageEmulsi = 1;
    let currentSearchEmulsi = '';
    let currentPerPageEmulsi = 5;

    // Search input handler
    $('#searchInputEmulsi').on('input', function() {
        clearTimeout(searchTimeoutEmulsi);
        const searchValue = $(this).val().trim();
        currentSearchEmulsi = searchValue;
        currentPageEmulsi = 1;
        
        // Show/hide clear button
        if (searchValue.length > 0) {
            $('#clearBtnEmulsi').show();
        } else {
            $('#clearBtnEmulsi').hide();
        }
        
        searchTimeoutEmulsi = setTimeout(function() {
            performSearchEmulsi();
        }, 300);
    });

    // Clear search
    $('#clearBtnEmulsi').on('click', function() {
        $('#searchInputEmulsi').val('');
        $(this).hide();
        currentSearchEmulsi = '';
        currentPageEmulsi = 1;
        performSearchEmulsi();
    });

    // Per page change
    $('#perPageSelectEmulsi').on('change', function() {
        currentPerPageEmulsi = $(this).val();
        currentPageEmulsi = 1;
        performSearchEmulsi();
    });

    // Pagination click handler
    $(document).on('click', '.pagination-emulsi .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            const urlParams = new URLSearchParams(url.split('?')[1]);
            const page = urlParams.get('page');
            if (page) {
                currentPageEmulsi = parseInt(page);
                performSearchEmulsi();
            }
        }
    });

    function performSearchEmulsi() {
        // Show loading
        $('#tableBody').html(`
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Sedang mencari data...</p>
                </td>
            </tr>
        `);

        $.ajax({
            url: '{{ route("persiapan-bahan-emulsi.search") }}',
            method: 'GET',
            data: {
                search: currentSearchEmulsi,
                page: currentPageEmulsi,
                per_page: currentPerPageEmulsi
            },
            success: function(response) {
                updateTableEmulsi(response);
                updatePaginationEmulsi(response.pagination);
                updateDataInfoEmulsi(response);
            },
            error: function(xhr) {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <div class="text-danger">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h5>Terjadi Kesalahan</h5>
                                <p>Gagal memuat data. Silakan coba lagi.</p>
                                <button class="btn btn-primary" onclick="location.reload()">
                                    <i class="fas fa-refresh"></i> Muat Ulang
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    function updateTableEmulsi(response) {
        let tableHtml = '';
        
        if (response.error) {
            tableHtml = `
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h5>Error: ${response.message}</h5>
                            <p>File: ${response.file}</p>
                            <p>Line: ${response.line}</p>
                        </div>
                    </td>
                </tr>
            `;
        } else if (response.data.length === 0) {
            tableHtml = `
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-4x text-muted opacity-50 mb-4"></i>
                            ${currentSearchEmulsi ? `
                                <h5 class="text-muted">Tidak ada data yang ditemukan</h5>
                                <p class="text-muted mb-4">Tidak ada data yang cocok dengan pencarian "<strong>${currentSearchEmulsi}</strong>".</p>
                                <button class="btn btn-outline-secondary mr-2" onclick="$('#searchInputEmulsi').val(''); $('#clearBtnEmulsi').click();">
                                    <i class="fas fa-times mr-1"></i> Hapus Filter
                                </button>
                            ` : `
                                <h5 class="text-muted">Belum ada data</h5>
                                <p class="text-muted mb-4">Silakan tambah data persiapan bahan emulsi terlebih dahulu.</p>
                            `}
                            @auth
                            @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-emulsi'))
                            <a href="{{ route('persiapan-bahan-emulsi.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i> Tambah Data
                            </a>
                            @endif
                            @endauth
                        </div>
                    </td>
                </tr>
            `;
        } else {
            response.data.forEach((item, index) => {
                const suhuEmulsiData = item.suhu_emulsi || [];
                const rowspan = suhuEmulsiData.length || 1;
                
                // Match the index page structure - 9 columns: No, Shift, Tanggal, Jam, Kode Produksi, Nama Produk, Nama Emulsi, Jumlah Proses Emulsi, Aksi
                const no = ((currentPageEmulsi - 1) * currentPerPageEmulsi) + index + 1;
                
                tableHtml += `
                    <tr>
                        <td class="text-center align-middle">
                            <span>${no}</span>
                        </td>
                        <td class="text-center align-middle">
                            ${item.shift_id == 1 ? '<span class="badge bg-primary">Shift 1</span>' :
                              item.shift_id == 2 ? '<span class="badge bg-success">Shift 2</span>' :
                              '<span class="badge bg-secondary">Shift ' + item.shift_id + '</span>'}
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-secondary">
                                ${item.tanggal ? (() => {
                                    const userRole = {{ auth()->check() ? auth()->user()->id_role : 'null' }};
                                    const showTime = [1, 2, 5].includes(userRole);
                                    const date = new Date(item.tanggal);
                                    if (showTime) {
                                        return date.toLocaleDateString('id-ID', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric'
                                        }) + ' ' + date.toLocaleTimeString('id-ID', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit'
                                        });
                                    } else {
                                        return date.toLocaleDateString('id-ID', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric'
                                        });
                                    }
                                })() : '-'}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span>
                                ${item.jam ? (() => {
                                    try {
                                        const jamParts = item.jam.split(':');
                                        return jamParts[0] + ':' + jamParts[1];
                                    } catch (e) {
                                        return item.jam;
                                    }
                                })() : '-'}
                            </span>
                        </td>
                        <td class="align-middle">
                            <span>${item.kode_produksi_emulsi || '-'}</span>
                        </td>
                        <td class="align-middle">
                           <span>${item.produk ? item.produk.nama_produk : '-'} ${item.berat_sampling ? item.berat_sampling + ' gram' : ''}</span>
                        </td>
                        <td class="align-middle">
                            <span>${item.nama_emulsi ? item.nama_emulsi.nama_emulsi : '-'}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span>${rowspan}</span>
                        </td>
                        <td class="align-middle">
                            <span>${item.user ? item.user.name : '-'}</span>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-vertical">
                                <!-- Tombol CRUD -->
                                <div class="mb-1">
                                    <a href="/paperless_futher/qc-sistem/persiapan-bahan-emulsi/${item.uuid}" class="btn btn-primary btn-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @auth
                                    @if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-emulsi'))
                                    <a href="/paperless_futher/qc-sistem/persiapan-bahan-emulsi/${item.uuid}/edit" class="btn btn-warning btn-sm" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-persiapan-bahan-emulsi'))
                                    <a href="/paperless_futher/qc-sistem/persiapan-bahan-emulsi/${item.uuid}/logs" class="btn btn-info btn-sm" title="History">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('delete-persiapan-bahan-emulsi'))
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-uuid="${item.uuid}" title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                    @endauth
                                </div>

                                <!-- Tombol Persetujuan berdasarkan Role -->
                                <div class="btn-group-vertical mb-1" role="group">
                                    ${generateApprovalButtonsEmulsi(item)}
                                </div>

                                <!-- Status Persetujuan -->
                                <div class="mt-1">
                                    ${generateApprovalStatusEmulsi(item)}
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#tableBody').html(tableHtml);
    }

    function updatePaginationEmulsi(pagination) {
        if (pagination && pagination.links) {
            $('.pagination-emulsi').html(pagination.links);
        }
    }

    function updateDataInfoEmulsi(response) {
        let infoText = 'Data Persiapan Bahan Emulsi';
        if (currentSearchEmulsi) {
            infoText = `Hasil pencarian: <span class="badge badge-warning"><i class="fas fa-filter mr-1"></i>${currentSearchEmulsi}</span>`;
        }
        $('#dataInfoEmulsi').html(infoText);
    }

    // Delete handler for emulsi
    $(document).on('click', '.delete-btn', function() {
        const uuid = $(this).data('uuid');
        
        if (confirm('Yakin ingin menghapus data ini?')) {
            const form = $('<form>', {
                'method': 'POST',
                'action': `{{ route('persiapan-bahan-emulsi.destroy', '') }}/${uuid}`
            });
            
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': csrfToken
            }));
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_method',
                'value': 'DELETE'
            }));
            
            $('body').append(form);
            form.submit();
        }
    });

    // Approval functions for Emulsi (make them available globally)
    window.generateApprovalButtonsEmulsi = function(item) {
        const userRole = {{ auth()->check() ? auth()->user()->id_role : 'null' }};
        let buttonsHtml = '';
        
        // Debug: log approval data
        console.log('Emulsi Approval data for item:', item.uuid, {
            approved_by_qc: item.approved_by_qc,
            approved_by_produksi: item.approved_by_produksi,
            approved_by_spv: item.approved_by_spv
        });

        if ([1, 5].includes(userRole)) {
            // Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_qc ? 'btn-success' : 'btn-outline-success'} approve-btn-emulsi" 
                        data-id="${item.uuid}" 
                        data-type="qc"
                        title="Disetujui oleh QC"
                        ${item.approved_by_qc ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_qc ? 'fa-check-circle' : 'fa-check'}"></i> QC
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_produksi ? 'btn-primary' : 'btn-secondary'}" 
                        title="${item.approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi'}"
                        disabled>
                    <i class="fas ${item.approved_by_produksi ? 'fa-check-circle' : 'fa-clock'}"></i> FM/FL PRODUKSI
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_spv ? 'btn-dark' : 'btn-secondary'}" 
                        title="${item.approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV'}"
                        disabled>
                    <i class="fas ${item.approved_by_spv ? 'fa-check-circle' : 'fa-clock'}"></i> SPV
                </button>
            `;
        } else if (userRole === 2) {
            // Role 2: Hanya tampilkan tombol Produksi
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_produksi ? 'btn-primary' : (item.approved_by_qc ? 'btn-outline-primary' : 'btn-secondary')} ${item.approved_by_qc && !item.approved_by_produksi ? 'approve-btn-emulsi' : ''}" 
                        data-id="${item.uuid}" 
                        data-type="produksi"
                        title="${!item.approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : (item.approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi')}"
                        ${!item.approved_by_qc || item.approved_by_produksi ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_produksi ? 'fa-check-circle' : (!item.approved_by_qc ? 'fa-clock' : 'fa-check')}"></i> Disetujui oleh Produksi
                </button>
            `;
        } else if (userRole === 3) {
            // Role 3: Hanya tampilkan tombol QC
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_qc ? 'btn-success' : 'btn-outline-success'} approve-btn-emulsi" 
                        data-id="${item.uuid}" 
                        data-type="qc"
                        title="Disetujui oleh QC"
                        ${item.approved_by_qc ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_qc ? 'fa-check-circle' : 'fa-check'}"></i> Disetujui oleh QC
                </button>
            `;
        } else if (userRole === 4) {
            // Role 4: Hanya tampilkan tombol SPV
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_spv ? 'btn-dark' : (item.approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary')} ${item.approved_by_produksi && !item.approved_by_spv ? 'approve-btn-emulsi' : ''}" 
                        data-id="${item.uuid}" 
                        data-type="spv"
                        title="${!item.approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : (item.approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV')}"
                        ${!item.approved_by_produksi || item.approved_by_spv ? 'disabled' : ''}>
                    <i class="fas ${item.approved_by_spv ? 'fa-check-circle' : (!item.approved_by_produksi ? 'fa-clock' : 'fa-check')}"></i> Disetujui oleh SPV
                </button>
            `;
        } else {
            // Role lain: Tampilkan semua tombol sebagai read-only
            buttonsHtml += `
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_qc ? 'btn-success' : 'btn-secondary'}" 
                        title="${item.approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC'}"
                        disabled>
                    <i class="fas ${item.approved_by_qc ? 'fa-check-circle' : 'fa-clock'}"></i> QC
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_produksi ? 'btn-primary' : 'btn-secondary'}" 
                        title="${item.approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi'}"
                        disabled>
                    <i class="fas ${item.approved_by_produksi ? 'fa-check-circle' : 'fa-clock'}"></i> Produksi
                </button>
                <button type="button" 
                        class="btn btn-sm ${item.approved_by_spv ? 'btn-dark' : 'btn-secondary'}" 
                        title="${item.approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV'}"
                        disabled>
                    <i class="fas ${item.approved_by_spv ? 'fa-check-circle' : 'fa-clock'}"></i> SPV
                </button>
            `;
        }

        return buttonsHtml;
    };

    window.generateApprovalStatusEmulsi = function(item) {
        let statusHtml = '';
        
        if (item.approved_by_qc) {
            statusHtml += '<small class="badge badge-success d-block mb-1">✓ QC</small>';
        }
        if (item.approved_by_produksi) {
            statusHtml += '<small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>';
        }
        if (item.approved_by_spv) {
            statusHtml += '<small class="badge badge-dark d-block mb-1">✓ SPV</small>';
        }
        
        return statusHtml;
    };

    // Approval button functionality for Emulsi
    $(document).on('click', '.approve-btn-emulsi', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);
        
        // Konfirmasi sebelum approve
        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi', 
            'spv': 'SPV'
        };
        
        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            // Disable button sementara dan show loading
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // AJAX request untuk approval
            $.ajax({
                url: `/paperless_futher/qc-sistem/persiapan-bahan-emulsi/${id}/approve`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                              .addClass('btn-success')
                              .html('<i class="fas fa-check-circle"></i> Approved');
                        
                        // Reload data setelah delay singkat
                        setTimeout(function() {
                            // Trigger search to refresh data
                            $('#searchInputEmulsi').trigger('input');
                        }, 1500);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        // Restore original button text
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> QC',
                            'produksi': '<i class="fas fa-check"></i> Fm/Fl PRODUKSI',
                            'spv': '<i class="fas fa-check"></i> SPV'
                        };
                        button.html(originalText[type]);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyetujui data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    button.prop('disabled', false);
                    // Restore original button text
                    const originalText = {
                        'qc': '<i class="fas fa-check"></i> QC',
                        'produksi': '<i class="fas fa-check"></i>FM/FL PRODUKSI',
                        'spv': '<i class="fas fa-check"></i> SPV'
                    };
                    button.html(originalText[type]);
                }
            });
        }
    });
});
// // Bagian Input Nilai Berat
var nilaiBerat = ['-',55,100, 200, 225, 250, 300, 315, 360, 400, 450, 500 , 700, 900, 1000 ,1100, 2000];
$(document).ready(function() {
    var $selects = $('#nilai_select_berat, #nilai_select_berat_sampling').add('[data-nilai-berat]');

    $selects.each(function() {
        var $select = $(this);
        var selectedValue = $select.data('selected');

        $select.empty();
        $select.append('<option value="">Pilih Nilai</option>');
        $.each(nilaiBerat, function(i, val) {
            $select.append('<option value="' + val + '">' + val + '</option>');
        });

        $select.select2({
            placeholder: "Pilih Nilai Berat"
        });

        if (selectedValue !== undefined && selectedValue !== null && selectedValue !== '') {
            $select.val(String(selectedValue)).trigger('change');
        }
    });
});

$(document).ready(function() {
       
        initSelectBeratEdit('{{ $pengemasanProduk->berat ?? '' }}', '#edit_nilai_select_berat');
        initSelectBeratEdit('{{ $dataBag->berat ?? '' }}', '#edit_nilai_select_berat_for_data_bag');
        initSelectBeratEdit('{{ $dataBox->berat ?? '' }}', '#edit_nilai_select_berat_for_data_box');
        initSelectBeratEdit('{{ $item->berat ?? '' }}', '#edit_nilai_select_berat_for_pemeriksaan_produk_cooking_mixer_fla');
        initSelectBeratEdit('{{ $item->berat_produk ?? '' }}', '#edit_nilai_select_berat_penggorengan');
        initSelectBeratEdit('{{ $inputRoasting->berat_produk ?? '' }}', '#edit_nilai_select_berat_roasting');
        initSelectBeratEdit('{{ $pemeriksaan->berat ?? '' }}', '#edit_nilai_select_berat_for_pemeriksaan_benda_asing');
    });


// Edit Input Nilai Berat
function initSelectBeratEdit(selectedValue = null, selector = $selector) {
    var $select = $(selector);
    $select.empty();
    $select.append('<option value="">Pilih Nilai</option>');
    $.each(nilaiBerat, function(i, val) {
        var selected = (selectedValue && selectedValue == val) ? 'selected' : '';
        $select.append('<option value="' + val + '" ' + selected + '>' + val + '</option>');
    });
    $select.select2({
        placeholder: "Pilih Nilai Berat"
    });
    if (selectedValue) {
        $select.val(selectedValue).trigger('change');
    }
}
// End

// All select 2 Produk
$(document).ready(function() {
    // Inisialisasi select2 untuk semua elemen dengan id 'id_produk' dan yang diawali 'id_produk_'
    $('select[id="id_produk"], select[id^="id_produk_"]').select2({
        placeholder: "Pilih Produk",
        allowClear: true,
    });
});
// End
// All select 2 Area
$(document).ready(function() {
    // Inisialisasi select2 untuk semua elemen dengan id 'id_produk' dan yang diawali 'id_produk_'
    $('select[id="id_area"], select[id^="id_area_"]').select2({
        placeholder: "Pilih Area",
        allowClear: true,
    });
});
// End
// All select 2 Barang
$(document).ready(function() {
    // Inisialisasi select2 untuk semua elemen dengan id 'id_produk' dan yang diawali 'id_produk_'
    $('select[id="id_barang"], select[id^="id_barang_"]').select2({
        placeholder: "Pilih Barang",
        allowClear: true,
    });
});
// End

// Global Select2 untuk Dropdown Defect

$(document).ready(function() {
    $('.select-defect').select2({
        placeholder: "Pilih Jenis Defect",
        allowClear: true,
        width: '100%',
        multiple: true,
        closeOnSelect: false
    });

     $('#id_jenis_breader_breader').select2({
        placeholder: "Pilih Jenis Breader",
        allowClear: true,
        width: '100%',
        multiple: true,
        closeOnSelect: false
    });

     $('#id_better_select_better').select2({
        placeholder: "Pilih Jenis Better",
        allowClear: true,
        width: '100%',
        multiple: true,
        closeOnSelect: false
    });

     $('#id_jenis_better_step3').select2({
        placeholder: "Pilih Jenis Better",
        allowClear: true,
        width: '100%',
        multiple: true,
        closeOnSelect: false
    });

    // ========== Dynamic Kode Produksi Better ==========
    $('#id_better_select_better, #id_jenis_better_step3').on('change', function() {
        var $wrapper = $('#kode_produksi_better_wrapper');
        var $container = $('#kode_produksi_better_container');
        if (!$container.length) return; // skip jika container tidak ada di halaman ini

        var selectedValues = $(this).val() || [];

        if (selectedValues.length === 0) {
            $wrapper.hide();
            $container.html(''); // Clear all inputs when hidden
            return;
        }

        $wrapper.show();

        // Simpan nilai lama
        var oldValues = {};
        $container.find('input[type="text"]').each(function() {
            var name = $(this).attr('name');
            if (name && name.indexOf('[') > -1) {
                var id = name.match(/\[(.*?)\]/)[1];
                oldValues[id] = $(this).val();
            }
        });

        var html = '';
        $(this).find('option:selected').each(function() {
            var id = $(this).val();
            var text = $(this).text();
            var val = oldValues[id] || '';
            html += '<div class="input-group mb-2">'
                + '<div class="input-group-prepend">'
                + '<span class="input-group-text" style="min-width: 180px; font-weight: 600;">' + text + '</span>'
                + '</div>'
                + '<input type="text" name="kode_produksi_better[' + id + ']" class="form-control kode-produksi-input" value="' + val + '" placeholder="Masukkan Kode Produksi" required>'
                + '</div>';
        });
        $container.html(html);
    });

    // ========== Dynamic Kode Produksi Breader ==========
    $('#id_jenis_breader_breader').on('change', function() {
        var $wrapper = $('#kode_produksi_breader_wrapper');
        var $container = $('#kode_produksi_breader_container');
        if (!$container.length) return; // skip jika container tidak ada di halaman ini

        var selectedValues = $(this).val() || [];

        if (selectedValues.length === 0) {
            $wrapper.hide();
            $container.html(''); // Clear all inputs when hidden
            return;
        }

        $wrapper.show();

        // Simpan nilai lama
        var oldValues = {};
        $container.find('input[type="text"]').each(function() {
            var name = $(this).attr('name');
            if (name && name.indexOf('[') > -1) {
                var id = name.match(/\[(.*?)\]/)[1];
                oldValues[id] = $(this).val();
            }
        });

        var html = '';
        $(this).find('option:selected').each(function() {
            var id = $(this).val();
            var text = $(this).text();
            var val = oldValues[id] || '';
            html += '<div class="input-group mb-2">'
                + '<div class="input-group-prepend">'
                + '<span class="input-group-text" style="min-width: 180px; font-weight: 600;">' + text + '</span>'
                + '</div>'
                + '<input type="text" name="kode_produksi_breader[' + id + ']" class="form-control kode-produksi-input" value="' + val + '" placeholder="Masukkan Kode Produksi" required>'
                + '</div>';
        });
        $container.html(html);
    });
});
// End




// Data Master Fla
$(document).ready(function() {
    // Check if we're on FLA pages
    const currentUrl = window.location.href;
    const isNamaFormulaFlaPage = currentUrl.includes('nama-formula-fla');
    const isNomorStepFlaPage = currentUrl.includes('nomor-step-formula-fla');
    const isBahanFormulaFlaPage = currentUrl.includes('bahan-formula-fla');
    
    // DataTable for nama-formula-fla and bahan-formula-fla index pages
    if ((isNamaFormulaFlaPage || isBahanFormulaFlaPage) && (currentUrl.includes('/index') || (!currentUrl.includes('/create') && !currentUrl.includes('/edit') && !currentUrl.includes('/show')))) {
        if ($('#example1').length) {
            $('#example1').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        }
    }
    
    // Checkbox limitation for nomor-step-formula-fla pages (create and edit)
    if (isNomorStepFlaPage && (currentUrl.includes('/create') || currentUrl.includes('/edit'))) {
        // Limit checkbox selection to maximum 2
        $('.proses-checkbox').on('change', function() {
            var checkedBoxes = $('.proses-checkbox:checked').length;
            
            if (checkedBoxes > 2) {
                $(this).prop('checked', false);
                alert('Maksimal hanya boleh memilih 2 proses!');
            }
        });

        // Cascading Dropdowns - Product to Formula (Nomor )
        $('#id_produk').on('change', function() {
            var productId = $(this).val();
            var formulaSelect = $('#id_nama_formula_fla');

            // Reset and disable dependent dropdown
            formulaSelect.html('<option value="">Pilih Nama Formula FLA</option>').prop('disabled', true);

            if (productId) {
                $.ajax({
                    url: '{{ url("/super-admin/ajax/formula-by-product") }}/' + productId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        formulaSelect.prop('disabled', false);
                        $.each(data, function(key, value) {
                            formulaSelect.append('<option value="' + value.id + '">' + value.nama_formula_fla + '</option>');
                        });

                        // Auto-select using old value (create) or current formula id (edit)
                        var oldFormulaId = formulaSelect.data('old-value');
                        var currentFormulaId = $('meta[name="current-formula-id"]').attr('content');
                        var selectedFormulaId = oldFormulaId || currentFormulaId;
                        if (selectedFormulaId) {
                            formulaSelect.val(selectedFormulaId).trigger('change');
                        }
                    },
                    error: function() {
                        alert('Error loading formula data');
                    }
                });
            }
        });

        // Trigger cascading if product already selected (e.g., validation error or edit)
        if ($('#id_produk').val()) {
            $('#id_produk').trigger('change');
        }
    }
    
    // Cascading dropdowns and dynamic forms for bahan-formula-fla pages
    if (isBahanFormulaFlaPage && (currentUrl.includes('/create') || currentUrl.includes('/edit'))) {
        
        // Cascading Dropdowns
        $('#id_produk').on('change', function() {
            var productId = $(this).val();
            var formulaSelect = $('#id_nama_formula_fla');
            var stepSelect = $('#id_stp_frm_fla');
            
            // Reset and disable dependent dropdowns
            formulaSelect.html('<option value="">Pilih Nama Formula FLA</option>').prop('disabled', true);
            stepSelect.html('<option value="">Pilih Step Formula FLA</option>').prop('disabled', true);
            
            if (productId) {
                $.ajax({
                    url: '{{ url("/super-admin/ajax/formula-by-product") }}/' + productId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        formulaSelect.prop('disabled', false);
                        $.each(data, function(key, value) {
                            formulaSelect.append('<option value="' + value.id + '">' + value.nama_formula_fla + '</option>');
                        });
                        
                        // Auto-select if editing and current formula matches
                        var currentFormulaId = $('meta[name="current-formula-id"]').attr('content');
                        if (currentFormulaId) {
                            formulaSelect.val(currentFormulaId).trigger('change');
                        }
                    },
                    error: function() {
                        alert('Error loading formula data');
                    }
                });
            }
        });
        
        $('#id_nama_formula_fla').on('change', function() {
            var formulaId = $(this).val();
            var stepSelect = $('#id_stp_frm_fla');
            
            // Reset step dropdown
            stepSelect.html('<option value="">Pilih Step Formula FLA</option>').prop('disabled', true);
            
            if (formulaId) {
                $.ajax({
                    url: '{{ url("/super-admin/ajax/steps-by-formula") }}/' + formulaId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        stepSelect.prop('disabled', false);
                        $.each(data, function(key, value) {
                            var prosesText = value.proses ? ' (' + value.proses + ')' : '';
                            stepSelect.append('<option value="' + value.id + '">Step ' + value.nomor_step + prosesText + '</option>');
                        });
                        
                        // Auto-select if editing and current step matches
                        var currentStepId = $('meta[name="current-step-id"]').attr('content');
                        if (currentStepId) {
                            stepSelect.val(currentStepId);
                        }
                    },
                    error: function() {
                        alert('Error loading step data');
                    }
                });
            }
        });
        
        // Initialize cascading dropdowns for edit page
        if (currentUrl.includes('/edit')) {
            var currentProductId = $('meta[name="current-product-id"]').attr('content');
            if (currentProductId) {
                $('#id_produk').val(currentProductId).trigger('change');
            }
        }
        
        // Dynamic Forms - Bahan Formula FLA
        $(document).on('click', '.add-bahan', function() {
            var newItem = `
                <div class="input-group mb-2 bahan-item">
                    <input type="text" name="bahan_formula_fla[]" 
                           class="form-control" 
                           placeholder="Masukkan bahan formula FLA" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-bahan">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#bahan-container').append(newItem);
        });
        
        $(document).on('click', '.remove-bahan', function() {
            $(this).closest('.bahan-item').remove();
        });
        
        // Dynamic Forms - Berat Formula FLA
        $(document).on('click', '.add-berat', function() {
            var newItem = `
                <div class="input-group mb-2 berat-item">
                    <input type="text" name="berat_formula_fla[]" 
                           class="form-control" 
                           placeholder="Masukkan berat formula FLA" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-berat">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#berat-container').append(newItem);
        });
        
        $(document).on('click', '.remove-berat', function() {
            $(this).closest('.berat-item').remove();
        });
    }
    
    // Pemeriksaan Produk Cooking Mixer FLA - Cascading dropdowns and detail table
    var isPemeriksaanProdukPage = currentUrl.includes('/qc-sistem/pemeriksaan-produk-cooking-mixer-fla');
    
    if (isPemeriksaanProdukPage && (currentUrl.includes('/create') || currentUrl.includes('/edit'))) {
        
        // Status Gas Switch Label Update
        $('#status_gas').on('change', function() {
            var label = $(this).is(':checked') ? 'Aktif' : 'Tidak Aktif';
            $('#status_gas_label').text(label);
        });
        
        // Cascading Dropdowns - Product to Formula
        $('#id_produk').on('change', function() {
            var productId = $(this).val();
            var formulaSelect = $('#id_nama_formula_fla');
            var detailContainer = $('#detail-table-container');
            
            // Reset and disable dependent dropdowns
            formulaSelect.html('<option value="">Pilih Nama Formula FLA</option>').prop('disabled', true);
            detailContainer.hide();
            
            if (productId) {
                $.ajax({
                    url: '{{ url("/qc-sistem/ajax/formula-by-product-pemeriksaan") }}/' + productId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        formulaSelect.prop('disabled', false);
                        $.each(data, function(key, value) {
                            formulaSelect.append('<option value="' + value.id + '">' + value.nama_formula_fla + '</option>');
                        });
                        
                        // Auto-select if editing and current formula matches
                        var currentFormulaId = $('meta[name="current-formula-id"]').attr('content');
                        if (currentFormulaId) {
                            formulaSelect.val(currentFormulaId).trigger('change');
                        }
                    },
                    error: function() {
                        alert('Error loading formula data');
                    }
                });
            }
        });
        
        // Formula to Detail Table and Bahan Selection (direct, no step required)
        $('#id_nama_formula_fla').on('change', function() {
            var formulaId = $(this).val();
            var detailContainer = $('#detail-table-container');
            var detailTableBody = $('#detail-table-body');
            var bahanInput = $('#id_frm_fla');
            
            // Hide detail table initially
            detailContainer.hide();
            bahanInput.val('');
            $('#id_stp_frm_fla').val('');
            
            if (formulaId) {
                $.ajax({
                    url: '{{ url("/qc-sistem/ajax/bahan-by-formula-pemeriksaan") }}/' + formulaId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            var data = response.data;
                            
                            // Set hidden fields
                            bahanInput.val(data.id);
                            $('#id_stp_frm_fla').val(data.id_nomor_step_formula_fla);
                            
                            // Build table rows
                            var tableRows = '';
                            var maxRows = Math.max(
                                data.bahan_formula_fla ? data.bahan_formula_fla.length : 0,
                                data.berat_formula_fla ? data.berat_formula_fla.length : 0
                            );
                            
                            var savedKode = window.existingKodeProduksiBahan || [];
                            
                            for (var i = 0; i < maxRows; i++) {
                                var bahan = data.bahan_formula_fla && data.bahan_formula_fla[i] ? data.bahan_formula_fla[i] : '-';
                                var berat = data.berat_formula_fla && data.berat_formula_fla[i] ? data.berat_formula_fla[i] + ' kg' : '-';
                                var valKode = (savedKode[i] !== undefined && savedKode[i] !== null) ? savedKode[i] : '';
                                
                                tableRows += '<tr>';
                                tableRows += '<td>' + bahan + '</td>';
                                tableRows += '<td>' + berat + '</td>';
                                tableRows += '<td><input type="text" name="kode_produksi_bahan[]" class="form-control form-control-sm" value="' + valKode + '" placeholder="Masukkan kode produksi bahan"></td>';
                                
                                // Only show proses and nama_rm in first row (step hidden)
                                if (i === 0) {
                                    // tableRows += '<td rowspan="' + maxRows + '"><span class="badge badge-info">Step ' + data.step + '</span></td>';
                                    tableRows += '<td rowspan="' + maxRows + '">' + data.proses + '</td>';
                                    tableRows += '<td rowspan="' + maxRows + '">' + data.nama_rm + '</td>';
                                }
                                
                                tableRows += '</tr>';
                            }
                            
                            detailTableBody.html(tableRows);
                            detailContainer.show();
                        } else {
                            alert('Data bahan tidak ditemukan untuk formula ini');
                        }
                    },
                    error: function() {
                        alert('Error loading bahan data');
                    }
                });
            }
        });
        
        // Initialize cascading dropdowns for edit page
        if (currentUrl.includes('/edit')) {
            var currentProductId = $('meta[name="current-product-id"]').attr('content');
            if (currentProductId) {
                $('#id_produk').val(currentProductId).trigger('change');
            }
        }
    }
});
// End



// toggle jumlah rm halaman Chillroom
$(function() {
    function toggleJumlahRMValue() {
        if ($('#jumlah_rm_aktual').val() === 'proper') {
            $('#jumlah-rm-value-container').hide();
        } else {
            $('#jumlah-rm-value-container').show();
        }
    }
    $('#jumlah_rm_aktual').on('change', toggleJumlahRMValue);
    toggleJumlahRMValue();
});
// tab pane
$(document).ready(function() {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        // e.target -> tab yang baru aktif
        // e.relatedTarget -> tab yang sebelumnya aktif

        // Hapus kelas background dari li parent tab yang baru aktif
        $(e.target).closest('li').removeClass('btn-primary btn-success');

        // Tambahkan kembali kelas background ke li parent tab yang sebelumnya aktif
        var previousTab = $(e.relatedTarget);
        if (previousTab.attr('id') === 'custom-tabs-four-bag-tab' || previousTab.attr('id') === 'frayer1-tab') {
            previousTab.closest('li').addClass('btn-primary');
        } else if (previousTab.attr('id') === 'custom-tabs-four-box-tab' || previousTab.attr('id') === 'frayer2-tab' || previousTab.attr('id') === 'frayer3-tab' || previousTab.attr('id') === 'frayer4-tab' || previousTab.attr('id') === 'frayer5-tab') {
            previousTab.closest('li').addClass('btn-success');
        }
    });
});
// ajax for super-admin data bahan emulsi
if ($('#nama_emulsi_id').length) {
$('#id_plan_select, #id_produk_select').on('change', function() {
    let planId = $('#id_plan_select').val();
    let produkId = $('#id_produk_select').val();
    if(planId && produkId) {
        $.ajax({
            url: '{{ route("get-emulsi-by-plan-produk") }}',
            type: 'GET',
            data: { id_plan: planId, id_produk: produkId },
            success: function(res) {
                let $emulsi = $('#nama_emulsi_id');
                $emulsi.empty();
                $emulsi.append('<option value="">Pilih Emulsi</option>');
                $.each(res, function(i, emulsi) {
                    $emulsi.append('<option value="'+emulsi.id+'">'+emulsi.nama_emulsi+'</option>');
                });
            }
        });
    } else {
        $('#nama_emulsi_id').empty().append('<option value="">Pilih Emulsi</option>');
    }
});

$('#nama_emulsi_id').on('change', function() {
    var emulsiId = $(this).val();
    $('#total_pemakaian_id').html('<option value="">Loading...</option>');
    if (emulsiId) {
        $.get('{{ url("super-admin/get-total-pemakaian-by-emulsi") }}/' + emulsiId, function(data) {
            var options = '<option value="">Pilih Total Pemakaian</option>';
            data.forEach(function(item) {
                options += '<option value="'+item.id+'">'+item.total_pemakaian+'</option>';
            });
            $('#total_pemakaian_id').html(options);
        });
    } else {
        $('#total_pemakaian_id').html('<option value="">Pilih Total Pemakaian</option>');
    }
});

}

function loadNomorEmulsi() {
    var planId = $('#id_plan_select').val();
    var produkId = $('#id_produk_select').val();
    var emulsiId = $('#nama_emulsi_id').val();
    var totalPemakaianId = $('#total_pemakaian_id').val();

    if(planId && produkId && emulsiId && totalPemakaianId) {
        $('#nomor_emulsi_id').html('<option value="">Loading...</option>');
        $.ajax({
            url: '{{ route("get-nomor-emulsi") }}',
            type: 'GET',
            data: {
                id_plan: planId,
                id_produk: produkId,
                nama_emulsi_id: emulsiId,
                total_pemakaian_id: totalPemakaianId
            },
            success: function(res) {
                var options = '<option value="">Pilih Nomor Emulsi</option>';
                res.forEach(function(item) {
                    options += '<option value="'+item.id+'">'+item.nomor_emulsi+'</option>';
                });
                $('#nomor_emulsi_id').html(options);
            }
        });
    } else {
        $('#nomor_emulsi_id').html('<option value="">Pilih Nomor Emulsi</option>');
    }
}
$('#total_pemakaian_id').on('change', function() {
    loadNomorEmulsi();
    var totalPemakaianId = $(this).val();
    $('#nomor_emulsi_id').html('<option value="">Loading...</option>');
    if (totalPemakaianId) {
        $.get('{{ url("super-admin/ajax/get-total-pemakaian-by-emulsi-produk") }}/' + produkId + '/' + emulsiId, function(data) {
            var options = '<option value="">Pilih Nomor Emulsi</option>';
            data.forEach(function(item) {
                options += '<option value="'+item.id+'">'+item.nomor_emulsi+'</option>';
            });
            $('#nomor_emulsi_id').html(options);
        });
    } else {
        $('#nomor_emulsi_id').html('<option value="">Pilih Nomor Emulsi</option>');
    }
});
// Skrip untuk modul Persiapan Bahan Emulsi AJAX QC Sistem
$(document).ready(function() {
    // 1. Produk -> Nama Emulsi
    $('#id_produk_emulsi').on('change', function() {
        var produkId = $(this).val();
        $('#nama_emulsi_id_emulsi, #total_pemakaian_id_emulsi, #proses_emulsi_id_emulsi').empty().append('<option value="">Pilih Opsi</option>');
        $('#bahan-emulsi-list').empty();

        if (produkId) {
            var url = "{{ route('get-emulsi-by-produk', ['id_produk' => ':id_produk']) }}".replace(':id_produk', produkId);
            $.getJSON(url, function(data) {
                $.each(data, function(key, value) {
                    $('#nama_emulsi_id_emulsi').append('<option value="' + value.id + '">' + value.nama_emulsi + '</option>');
                });
            });
        }
    });

    // 2. Nama Emulsi -> Total Pemakaian
    $('#nama_emulsi_id_emulsi').on('change', function() {
        var produkId = $('#id_produk_emulsi').val();
        var emulsiId = $(this).val();
        $('#total_pemakaian_id_emulsi, #proses_emulsi_id_emulsi').empty().append('<option value="">Pilih Opsi</option>');
        $('#bahan-emulsi-list').empty();

        if (produkId && emulsiId) {
            var url = "{{ route('get-total-pemakaian-by-emulsi-produk', ['id_produk' => ':id_produk', 'nama_emulsi_id' => ':nama_emulsi_id']) }}";
            url = url.replace(':id_produk', produkId).replace(':nama_emulsi_id', emulsiId);
            $.getJSON(url, function(data) {
                $.each(data, function(key, value) {
                    $('#total_pemakaian_id_emulsi').append('<option value="' + value.id + '">' + value.total_pemakaian + '</option>');
                });
            });
        }
    });
    // 3. Total Pemakaian -> Proses Emulsi
     $('#total_pemakaian_id_emulsi').on('change', function() {
        console.log('=== TOTAL PEMAKAIAN CHANGE ===');
        
        var totalPemakaianId = $(this).val();
        console.log('totalPemakaianId:', totalPemakaianId);
        
        $('#proses_emulsi_id_emulsi').empty().append('<option value="">Pilih Opsi</option>');
        $('#bahan-emulsi-list').empty();

        if (totalPemakaianId) {
            var url = "{{ route('get-nomor-emulsi-by-total-pemakaian', ['total_pemakaian_id' => ':id']) }}".replace(':id', totalPemakaianId);
            console.log('AJAX URL:', url);
            
            $.getJSON(url, function(data) {
                console.log('✅ Step 3 AJAX SUCCESS:', data);
                
                $.each(data, function(key, value) {
                    console.log('Adding option:', value.id, value.nomor_emulsi);
                    $('#proses_emulsi_id_emulsi').append('<option value="' + value.id + '" data-jumlah="' + value.nomor_emulsi + '">' + value.nomor_emulsi + '</option>');
                });
                
                console.log('Final options count:', $('#proses_emulsi_id_emulsi option').length);
            }).fail(function(xhr, status, error) {
                console.error('❌ Step 3 AJAX FAILED:', error);
            });
        }
    });

    // 4. Proses Emulsi -> Isi Multiple Tabel Bahan
    $('#proses_emulsi_id_emulsi').on('change', function() {
        console.log('=== PROSES EMULSI CHANGE EVENT TRIGGERED ===');
        
        var nomorEmulsiId = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var selectedText = selectedOption.text();
        var dataJumlah = selectedOption.data('jumlah');
        var jumlahProses = parseInt(dataJumlah) || parseInt(selectedText) || 1;
        var container = $('#multiple-tables-container'); // new container        
        
        // DETAILED DEBUG
        console.log('📋 Debug Info:');
        console.log('  - nomorEmulsiId:', nomorEmulsiId);
        console.log('  - selectedText:', selectedText);
        console.log('  - dataJumlah:', dataJumlah);
        console.log('  - jumlahProses:', jumlahProses);
        console.log('  - container.length:', container.length);
        console.log('  - Kondisi IF akan:', (nomorEmulsiId && jumlahProses) ? 'BERHASIL' : 'GAGAL');
        
        // Clear existing content
        // Clear existing content - IMPROVED VERSION
        console.log('🗑️ Starting to clear existing content...');

        // Method 1: Clear dari berbagai kemungkinan lokasi
        $('#multiple-tables-container').empty();
        $('.multiple-tables-container').remove(); // Global selector
        container.find('.multiple-tables-container').remove(); // Di dalam container
        container.siblings('.multiple-tables-container').remove(); // Sibling container

        // Method 2: Clear berdasarkan class dan content
        container.parent().find('.mt-4:has(h5:contains("Proses Emulsi"))').remove();
        container.parent().find('h5:contains("Proses Emulsi ke-")').parent().remove();

        // Method 3: Clear semua div dengan class mt-4 yang berisi tabel
        container.parent().find('.mt-4').each(function() {
            if ($(this).find('table').length > 0 && $(this).find('h5:contains("Proses Emulsi")').length > 0) {
                $(this).remove();
            }
        });

        console.log('✅ All existing content cleared completely');

        if (nomorEmulsiId && jumlahProses) {
            console.log('✅ Kondisi IF berhasil, memulai AJAX call...');
            
            var url = "{{ route('get-bahan-emulsi-by-nomor-emulsi', ['nomor_emulsi_id' => ':id']) }}".replace(':id', nomorEmulsiId);
            console.log('🌐 AJAX URL:', url);
            
            $.getJSON(url, function(data) {
                console.log('✅ AJAX SUCCESS - Data received:', data);
                console.log('📊 Data length:', data.length);
                
                if (data.length > 0) {
                    console.log('🔄 Starting table generation...');
                    
                    var multipleTablesHtml = '<div class="multiple-tables-container">';
                    
                    // Loop untuk membuat multiple tabel
                    for (let prosesKe = 1; prosesKe <= jumlahProses; prosesKe++) {
                        console.log(`📝 Generating table for proses ke-${prosesKe}`);
                        
                        multipleTablesHtml += `
                            <div class="mt-4">
                                <h5 class="text-primary">Proses Emulsi ke-${prosesKe}</h5>
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="25%">Nama RM</th>
                                            <th width="30%">Berat (gram)</th>
                                            <th width="20%">Kode Produksi Bahan</th>
                                            <th width="20%">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                        
                        // Loop untuk mengisi baris dalam tabel
                        $.each(data, function(index, item) {
                            console.log(`  - Adding row ${index + 1}: ${item.nama_rm}`);
                            
                            const uniqueId = `berat_${prosesKe}_${index}`;
                            
                            multipleTablesHtml += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.nama_rm}
                                        <input type="hidden" name="bahan_emulsi_id[${prosesKe-1}][]" value="${item.id}">
                                        <input type="hidden" name="proses_ke[${prosesKe-1}][]" value="${prosesKe}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <select class="form-control berat-source-select" data-unique-id="${uniqueId}" data-proses="${prosesKe-1}" data-index="${index}" data-db-value="${item.berat_rm}">
                                                <option value="db">Master (${item.berat_rm})</option>
                                                <option value="manual">Manual Input</option>
                                            </select>
                                            <input type="number" id="${uniqueId}" name="berat_rm[${prosesKe-1}][]" class="form-control berat-input" value="${item.berat_rm}" step="0.1" placeholder="Berat" disabled>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="kode_produksi_bahan[${prosesKe-1}][]" class="form-control" placeholder="Masukkan Kode Produksi">
                                    </td>
                                    <td>
                                        <select name="suhu[${prosesKe-1}][]" class="form-control">
                                            <option value="✔">✔ OK</option>
                                            <option value="✘">✘ Tidak OK</option>
                                        </select>
                                    </td>
                                </tr>`;
                        });
                        
                        // Event handler untuk toggle berat source
                        $(document).on('change', '.berat-source-select', function() {
                            const uniqueId = $(this).data('unique-id');
                            const dbValue = $(this).data('db-value');
                            const sourceValue = $(this).val();
                            const inputField = $(`#${uniqueId}`);
                            
                            if (sourceValue === 'db') {
                                inputField.val(dbValue).prop('disabled', true);
                            } else {
                                inputField.val('').prop('disabled', false).focus();
                            }
                        });
                        
                        // TAMBAHAN BARU: Row untuk Suhu (Full Width)
                        multipleTablesHtml += `
                                <tr class="bg-light">
                                    <td colspan="5">
                                        <div class="row align-items-center">
                                            <label class="col-sm-2 col-form-label font-weight-bold mb-0">Suhu</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="kondisi_proses[${prosesKe-1}]" class="form-control" placeholder="Masukkan Suhu" required>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;
                        
                        // TAMBAHAN BARU: Row untuk Hasil Emulsi (Full Width)
                        multipleTablesHtml += `
                                <tr class="bg-light">
                                    <td colspan="5">
                                        <div class="row align-items-center">
                                            <label class="col-sm-2 col-form-label font-weight-bold mb-0">Hasil Emulsi</label>
                                            <div class="col-sm-10">
                                                <select name="hasil_emulsi_proses[${prosesKe-1}]" class="form-control" required>
                                                    <option value="">Pilih Hasil</option>
                                                    <option value="✔">✔ OK</option>
                                                    <option value="✘">✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;
                        
                        multipleTablesHtml += `
                                    </tbody>
                                </table>
                            </div>`;
                    }
                    
                    multipleTablesHtml += '</div>';
                    
                    console.log('🎯 Appending HTML to container...');
                    console.log('📄 Generated HTML length:', multipleTablesHtml.length);
                    
                    container.html(multipleTablesHtml);
                    
                    console.log('✅ Tables generated successfully!');
                    console.log('🔍 Checking generated inputs...');
                    
                    // Verify generated inputs
                    setTimeout(function() {
                        var bahanInputs = $('input[name^="bahan_emulsi_id"]').length;
                        var suhuInputs = $('select[name^="suhu"]').length;
                        var prosesInputs = $('input[name^="proses_ke"]').length;
                        
                        console.log('📊 Generated inputs count:');
                        console.log('  - bahan_emulsi_id inputs:', bahanInputs);
                        console.log('  - suhu selects:', suhuInputs);
                        console.log('  - proses_ke inputs:', prosesInputs);
                    }, 100);
                    
                } else {
                    console.log('⚠️ No data received from AJAX');
                }
            }).fail(function(xhr, status, error) {
                console.error('❌ AJAX FAILED:');
                console.error('  - Status:', status);
                console.error('  - Error:', error);
                console.error('  - Response:', xhr.responseText);
            });
        } else {
            console.log('❌ Kondisi IF gagal:');
            console.log('  - nomorEmulsiId valid:', !!nomorEmulsiId);
            console.log('  - jumlahProses valid:', !!jumlahProses);
        }
        
        console.log('=== END PROSES EMULSI DEBUG ===');
    });
});

$(function() {
    function loadBetter() {
        var plan = $('#id_plan_select').val();
        var produk = $('#id_produk_select').val();
        var $better = $('#id_better_select');
        $better.html('<option value="">Pilih Better</option>');
        if(plan && produk) {
            $.get("{{ route('ajax.better-by-plan-produk') }}", {id_plan: plan, id_produk: produk}, function(res) {
                $.each(res, function(i, better) {
                    $better.append('<option value="'+better.id+'">'+(better.nama_formula_better || better.nama_better)+'</option>');
                });
            });
        }
    }
    $('#id_plan_select, #id_produk_select').change(loadBetter);
});
$(function() {
    function resizeBetterFormulaTextarea(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = (el.scrollHeight) + 'px';
    }

    function normalizeBetterItems(raw) {
        if (!raw) return [];
        if (Array.isArray(raw)) return raw;
        if (typeof raw === 'string') {
            try {
                var parsed = JSON.parse(raw);
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }
        return [];
    }

    $(document).off('change.betterBeratModeGlobal').on('change.betterBeratModeGlobal', '.better-berat-mode', function() {
        var mode = $(this).val();
        var $td = $(this).closest('td');
        var $input = $td.find('.better-berat-input');
        if (mode === 'manual') {
            $input.prop('readonly', false);
            if ($input.val() === $input.attr('data-master')) {
                $input.val('');
            }
            $input.trigger('focus');
        } else {
            $input.prop('readonly', true);
            $input.val($input.attr('data-master') ?? '');
        }
    });

    // Filter better by produk (khusus tab better)
    $('#id_produk_select_better').change(function() {
        var produk = $(this).val();
        var $better = $('#id_better_select_better');
        $better.html('<option value="">Pilih Jenis Better</option>');
        $('#std-aktual-table-better').html('');
        $('#better-input-table-wrapper').hide();
        $('#sensori-wrapper').hide();
        $('#better-input-rows').html('');
        $('input[name="kode_produksi_better"]').val('');
        if(produk) {
            $.get("{{ route('ajax.better-by-produk-better') }}", {id_produk: produk}, function(res) {
                $.each(res, function(i, better) {
                    $better.append('<option value="'+better.id+'">'+(better.nama_formula_better || better.nama_better)+'</option>');
                });
            });
        }
    });

// Load std & input aktual by better (khusus tab better)
$('#id_better_select_better').change(function() {
        var better = $(this).val();
        $('#std-aktual-table-better').html('');
        $('#better-input-table-wrapper').hide();
        $('#sensori-wrapper').hide();
        $('#better-input-rows').html('');

        if(better && (Array.isArray(better) ? better.length > 0 : better !== '')) {
            $('#better-input-table-wrapper').show();
            $('#sensori-wrapper').show();

            // Jika multiple, kita ambil detail yang pertama atau sesuaikan logic
            // Untuk saat ini kita ambil detail yang pertama jika array
            var targetId = Array.isArray(better) ? better[0] : better;
            $.get("{{ route('ajax.better-detail-better') }}", {id_better: targetId}, function(detail) {
                var items = normalizeBetterItems(detail.better_items);
                var $rows = $('#better-input-rows');
                $rows.html('');

                if (items.length === 0) {
                    items = [{
                        berat: (detail.berat ?? ''),
                        nama_formula_better: (detail.nama_formula_better ?? '')
                    }];
                }

                $.each(items, function(i, it) {
                    var formulaVal = (it && it.nama_formula_better !== undefined && it.nama_formula_better !== null)
                        ? String(it.nama_formula_better)
                        : '';
                    var beratVal = (it && it.berat !== undefined && it.berat !== null)
                        ? String(it.berat)
                        : '';

                    var beratLabel = beratVal !== '' ? ('Master (' + beratVal + ')') : 'Master';

                    var currentSuhu = $('#global_suhu_air').val();
                    var currentSensori = $('#global_sensori').val();

                    var tr = '<tr>'
                        + '<td>'
                        + '<textarea class="form-control form-control-sm better-master-formula" rows="1" style="resize:none; overflow:hidden;" readonly>'
                        + (formulaVal ? $('<div/>').text(formulaVal).html() : '')
                        + '</textarea>'
                        + '<input type="hidden" name="better_rows['+i+'][master_nama_formula_better]" value="'+$('<div/>').text(formulaVal).html()+'">'
                        + '<input type="hidden" name="better_rows['+i+'][suhu_air]" class="row_suhu_air" value="'+currentSuhu+'">'
                        + '<input type="hidden" name="better_rows['+i+'][sensori]" class="row_sensori" value="'+currentSensori+'">'
                        + '</td>'
                        + '<td>'
                        + '<input type="text" class="form-control form-control-sm" name="better_rows['+i+'][kode_produksi_better]" placeholder="Masukkan Kode Produksi">'
                        + '</td>'
                        + '<td>'
                        + '<div class="input-group input-group-sm">'
                        + '<div class="input-group-prepend" style="min-width: 120px;">'
                        + '<select class="form-control better-berat-mode" name="better_rows['+i+'][berat_mode]" data-row="'+i+'">'
                        + '<option value="master" selected>'+beratLabel+'</option>'
                        + '<option value="manual">Manual</option>'
                        + '</select>'
                        + '</div>'
                        + '<input type="number" step="0.01" class="form-control form-control-sm better-berat-input" name="better_rows['+i+'][master_berat]" value="'+$('<div/>').text(beratVal).html()+'" data-master="'+$('<div/>').text(beratVal).html()+'" readonly required>'
                        + '</div>'
                        + '</td>'
                        + '</tr>';

                    $rows.append(tr);
                });

                $rows.find('textarea.better-master-formula').each(function() {
                    resizeBetterFormulaTextarea(this);
                });
            });

            $('#better-input-rows').off('change.betterBeratMode').on('change.betterBeratMode', '.better-berat-mode', function() {
                var mode = $(this).val();
                var $td = $(this).closest('td');
                var $input = $td.find('.better-berat-input');
                if (mode === 'manual') {
                    $input.prop('readonly', false);
                    if ($input.val() === $input.attr('data-master')) {
                        $input.val('');
                    }
                    $input.trigger('focus');
                } else {
                    $input.prop('readonly', true);
                    $input.val($input.attr('data-master') ?? '');
                }
            });

            $.get("{{ route('ajax.std-by-better-better') }}", {id_better: better}, function(res) {
                if(res.length > 0) {
                    var html = '';
                    $.each(res, function(i, std) {
                        html += '<div class="row align-items-center mb-3">'
                            + '<div class="col-md-3"><label class="mb-0">Standar Viscositas (detik)</label></div>'
                            + '<div class="col-md-3"><div class="d-flex align-items-center"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><input type="text" class="form-control form-control-sm bg-light" value="'+std.std_viskositas+'" readonly></div></div>'
                            + '<div class="col-md-3"><label class="mb-0">Aktual Viscositas (detik)</label></div>'
                            + '<div class="col-md-3">'
                            + '<input type="hidden" name="id_std_salinitas_viskositas[]" value="'+std.id+'">'
                            + '<input type="text" name="aktual_vis[]" class="form-control form-control-sm" required>'
                            + '</div>'
                            + '</div>';
                        
                        html += '<div class="row align-items-center mb-3">'
                            + '<div class="col-md-3"><label class="mb-0">Standar Salinity (%)</label></div>'
                            + '<div class="col-md-3"><div class="d-flex align-items-center"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><input type="text" class="form-control form-control-sm bg-light" value="'+std.std_salinitas+'" readonly></div></div>'
                            + '<div class="col-md-3"><label class="mb-0">Aktual Salinity (%)</label></div>'
                            + '<div class="col-md-3"><input type="text" name="aktual_sal[]" class="form-control form-control-sm" required></div>'
                            + '</div>';
                            
                        html += '<div class="row align-items-center mb-3">'
                            + '<div class="col-md-3"><label class="mb-0">Suhu Akhir (°C)</label></div>'
                            + '<div class="col-md-9"><input type="text" name="aktual_suhu_air[]" class="form-control form-control-sm" required></div>'
                            + '</div>';
                    });
                    $('#std-aktual-table-better').html(html);
                }
            });
        }
    });

    $(document).on('input', '#global_suhu_air', function() {
        $('.row_suhu_air').val($(this).val());
    });
    
    $(document).on('change', '#global_sensori', function() {
        $('.row_sensori').val($(this).val());
    });
});
$('#id_produk_select').on('change', function() {
    var produkId = $(this).val();
    $('#id_suhu_frayer_select').html('<option value="">Memuat...</option>');
    if(produkId) {
        $.get('{{ url("super-admin/ajax/suhu-frayer-by-produk") }}/' + produkId, function(data) {            
            var options = '<option value="">Pilih Suhu Frayer</option>';
            data.forEach(function(item) {
                options += `<option value="${item.id}">${item.suhu_frayer}</option>`;
            });
            $('#id_suhu_frayer_select').html(options);
        });
    } else {
        $('#id_suhu_frayer_select').html('<option value="">Pilih Suhu Frayer</option>');
    }
});
$(document).ready(function () {
    if (
        window.location.pathname.includes('/std-fan/create') ||
        (window.location.pathname.match(/\/std-fan\//) && window.location.pathname.match(/\/edit$/))
    ) {
        var $planSelect = $('#id_plan_std_fan');
        var $produkSelect = $('#id_produk_std_fan');
        var $suhuBlokSelect = $('#id_suhu_blok_std_fan');

        function resetDropdown($dropdown, pesan) {
            $dropdown.html('<option value="">' + pesan + '</option>').prop('disabled', true);
        }

        // Event 1: PLAN -> PRODUK
        $planSelect.on('change', function () {
            var planId = $(this).val();
            resetDropdown($produkSelect, 'Memuat data...');
            resetDropdown($suhuBlokSelect, 'Pilih Produk terlebih dahulu'); // Reset suhu blok juga
            
            if (planId) {
                $.ajax({
                    url: '{{ url("/qc-sistem/ajax/produk-by-plan") }}/' + planId,
                    type: 'GET',
                    success: function (data) {
                        if (data.length > 0) {
                            var options = '<option value="">Pilih Produk</option>';
                            $.each(data, function (i, produk) {
                                options += '<option value="' + produk.id + '">' + produk.nama_produk + '</option>';
                            });
                            $produkSelect.html(options).prop('disabled', false);
                        } else {
                            resetDropdown($produkSelect, 'Produk tidak tersedia');
                        }
                    },
                    error: function () {
                        resetDropdown($produkSelect, 'Gagal memuat data');
                    }
                });
            } else {
                resetDropdown($produkSelect, 'Pilih Plan terlebih dahulu');
            }
        });

        // Event 2: PRODUK -> SUHU BLOK
        $produkSelect.on('change', function () {
            var produkId = $(this).val();
            var planId = $planSelect.val();
            resetDropdown($suhuBlokSelect, 'Memuat data...');

            if (produkId && planId) {
                $.ajax({
                    url: '{{ url("/qc-sistem/ajax/get-suhu-blok-by-produk") }}/' + produkId + '?plan_id=' + planId,
                    type: 'GET',
                    success: function (data) {
                        if (data.length > 0) {
                            var options = '<option value="">Pilih Suhu Blok</option>';
                            $.each(data, function (i, suhu) {
                                options += '<option value="' + suhu.id + '">' + suhu.suhu_blok + '</option>';
                            });
                            $suhuBlokSelect.html(options).prop('disabled', false);
                        } else {
                            resetDropdown($suhuBlokSelect, 'Suhu Blok tidak tersedia');
                        }
                    },
                    error: function () {
                        resetDropdown($suhuBlokSelect, 'Gagal memuat data');
                    }
                });
            } else {
                resetDropdown($suhuBlokSelect, 'Pilih Produk terlebih dahulu');
            }
        });
    }
});
$('#id_produk_breader').on('change', function() {
    var produkId = $(this).val();
    if (produkId) {
        $.ajax({
            url: '{{ url("qc-sistem/ajax/jenis-breader-by-produk") }}/' + produkId,            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#id_jenis_breader_breader').empty().append('<option value="">Pilih Jenis Breader</option>');
                $.each(data, function(key, value) {
                    $('#id_jenis_breader_breader').append('<option value="'+ value.id +'">'+ value.jenis_breader +'</option>');
                });
            }
        });
    } else {
        $('#id_jenis_breader_breader').empty().append('<option value="">Pilih Jenis Breader</option>');
    }
});

// Waktu Penggorengan 2 Cascading Dropdown Super admin
$(document).ready(function() {
    // Cascading dropdown for Suhu Frayer 2 based on Produk
    $('#id_produk').change(function() {
        var id_produk = $(this).val();
        if (id_produk && ($('#id_suhu_frayer_2').length > 0)) {
            let url = "{{ route('ajax.get-suhu-frayer-2-by-produk', ['id_produk' => ':id_produk']) }}";
            url = url.replace(':id_produk', id_produk);

            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('#id_suhu_frayer_2').empty().removeAttr('disabled');
                    $('#id_suhu_frayer_2').append('<option value="">-- Pilih Suhu Frayer 2 --</option>');
                    
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            // Debug: log data untuk melihat struktur
                            console.log('Suhu data:', value);
                            
                            // Handle berbagai kemungkinan format data
                            var suhuValue = '';
                            if (value.suhu_frayer_2 !== null && value.suhu_frayer_2 !== undefined) {
                                if (!isNaN(value.suhu_frayer_2) && value.suhu_frayer_2 !== '') {
                                    suhuValue = Math.round(parseFloat(value.suhu_frayer_2)) + '°C';
                                } else {
                                    suhuValue = value.suhu_frayer_2 + '°C';
                                }
                            } else {
                                suhuValue = 'N/A';
                            }
                            
                            $('#id_suhu_frayer_2').append('<option value="' + value.id + '">' + suhuValue + '</option>');
                        });
                    } else {
                        $('#id_suhu_frayer_2').append('<option value="">-- Tidak ada data --</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error loading Suhu Frayer 2 data:', error);
                    console.log('Response:', xhr.responseText);
                    $('#id_suhu_frayer_2').empty().attr('disabled', true);
                    $('#id_suhu_frayer_2').append('<option value="">-- Error loading data --</option>');
                }
            });
        } else {
            $('#id_suhu_frayer_2').empty().attr('disabled', true);
            $('#id_suhu_frayer_2').append('<option value="">-- Pilih Produk Terlebih Dahulu --</option>');
        }
    });
});

// AJAX khusus untuk form Frayer 1 di halaman Proses Frayer qc sistem
$(document).ready(function() {
    // Cascading dropdown for Suhu Frayer based on Produk (Frayer 1)
    $('#id_produk_f2').change(function() {
        var id_produk = $(this).val();
        if (id_produk) {
            $.ajax({
                type: "GET",
                url: "{{ url('qc-sistem/ajax/get-suhu-frayer-1-by-produk') }}/" + id_produk,                success: function(data) {
                    $('#id_suhu_frayer_1_f2').empty().removeAttr('disabled');
                    $('#id_suhu_frayer_1_f2').append('<option value="">Pilih Suhu Frayer</option>');
                    $.each(data, function(key, value) {
                        $('#id_suhu_frayer_1_f2').append('<option value="' + value.id + '">' + value.display + '</option>'); // ✅ UBAH INI
                    });
                    $('#id_waktu_penggorengan_f2').empty().attr('disabled', true);
                    $('#id_waktu_penggorengan_f2').append('<option value="">Pilih Waktu Penggorengan</option>');
                }
            });
        } else {
            $('#id_suhu_frayer_1_f2').empty().attr('disabled', true);
            $('#id_waktu_penggorengan_f2').empty().attr('disabled', true);
        }
    });

    // Cascading dropdown for Waktu Penggorengan based on Suhu Frayer (Frayer 2)
    $('#id_suhu_frayer_1_f2').change(function() {
        var id_suhu = $(this).val();
        if (id_suhu) {
            let url = "{{ route('get-waktu-penggorengan-by-suhu', ['id_suhu' => ':id_suhu']) }}";
            url = url.replace(':id_suhu', id_suhu);
            
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    $('#id_waktu_penggorengan_f2').empty().removeAttr('disabled');
                    $('#id_waktu_penggorengan_f2').append('<option value="">Pilih Waktu Penggorengan</option>');
                    $.each(data, function(key, value) {
                        $('#id_waktu_penggorengan_f2').append('<option value="' + value.id + '">' + value.waktu_penggorengan + ' detik</option>');
                    });
                }
            });
        } else {
            $('#id_waktu_penggorengan_f2').empty().attr('disabled', true);
        }
    });
});

// AJAX khusus untuk form Frayer 2 di halaman Proses Frayer qc sistem
$(document).ready(function() {
        // Cascading untuk Produk → Suhu Frayer 2 (form Frayer 2)
        $('#id_produk_f3').change(function() {
            var id_produk = $(this).val();
            if (id_produk) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('qc-sistem/ajax/get-suhu-frayer-2-by-produk') }}/" + id_produk,
                    success: function(data) {
                        $('#id_suhu_frayer_2').empty().removeAttr('disabled');
                        $('#id_suhu_frayer_2').append('<option value="">Pilih Suhu Frayer 2</option>');
                        $.each(data, function(key, value) {
                            $('#id_suhu_frayer_2').append('<option value="' + value.id + '">' + value.suhu_frayer_2 + '°C</option>');
                        });
                        
                        // Reset waktu penggorengan 2
                        $('#id_waktu_penggorengan_2').empty().attr('disabled', true);
                        $('#id_waktu_penggorengan_2').append('<option value="">Pilih Waktu Penggorengan</option>');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Suhu Frayer 2:', error);
                        alert('Gagal memuat data Suhu Frayer 2');
                    }
                });
            } else {
                $('#id_suhu_frayer_2').empty().attr('disabled', true);
                $('#id_waktu_penggorengan_2').empty().attr('disabled', true);
            }
        });

        // Cascading untuk Suhu Frayer 2 → Waktu Penggorengan 2
        $('#id_suhu_frayer_2').change(function() {
            var id_suhu = $(this).val();
            if (id_suhu) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('qc-sistem/ajax/get-waktu-penggorengan-2-by-suhu') }}/" + id_suhu,
                    success: function(data) {
                        $('#id_waktu_penggorengan_2').empty().removeAttr('disabled');
                        $('#id_waktu_penggorengan_2').append('<option value="">Pilih Waktu Penggorengan</option>');
                        $.each(data, function(key, value) {
                            $('#id_waktu_penggorengan_2').append('<option value="' + value.id + '">' + value.waktu_penggorengan_2 + ' detik</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Waktu Penggorengan 2:', error);
                        alert('Gagal memuat data Waktu Penggorengan 2');
                    }
                });
            } else {
                $('#id_waktu_penggorengan_2').empty().attr('disabled', true);
            }
        });
});

// AJAX khusus untuk form Frayer 3 di halaman Proses Frayer qc sistem
$(document).ready(function() {
        // Cascading untuk Produk → Suhu Frayer (form Frayer 3)
        $('#id_produk_f4').change(function() {
            var id_produk = $(this).val();
            if (id_produk) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('qc-sistem/ajax/get-suhu-frayer-3-by-produk') }}/" + id_produk,
                        success: function(data) {
                        $('#id_suhu_frayer').empty().removeAttr('disabled');
                        $('#id_suhu_frayer').append('<option value="">Pilih Suhu Frayer</option>');
                        $.each(data, function(key, value) {
                            $('#id_suhu_frayer').append('<option value="' + value.id + '">' + value.display + '</option>');
                        });
                        
                        // Reset waktu penggorengan
                        $('#id_waktu_penggorengan').empty().attr('disabled', true);
                        $('#id_waktu_penggorengan').append('<option value="">Pilih Waktu Penggorengan</option>');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Suhu Frayer:', error);
                        alert('Gagal memuat data Suhu Frayer');
                    }
                });
            } else {
                $('#id_suhu_frayer').empty().attr('disabled', true);
                $('#id_waktu_penggorengan').empty().attr('disabled', true);
            }
        });

        // Cascading untuk Suhu Frayer → Waktu Penggorengan (form Frayer 3)
        $('#id_suhu_frayer').change(function() {
            var id_suhu = $(this).val();
            if (id_suhu) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('qc-sistem/ajax/get-waktu-penggorengan-3-by-suhu') }}/" + id_suhu,
                    success: function(data) {
                        $('#id_waktu_penggorengan').empty().removeAttr('disabled');
                        $('#id_waktu_penggorengan').append('<option value="">Pilih Waktu Penggorengan</option>');
                        $.each(data, function(key, value) {
                            $('#id_waktu_penggorengan').append('<option value="' + value.id + '">' + value.waktu_penggorengan + ' detik</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Waktu Penggorengan:', error);
                        alert('Gagal memuat data Waktu Penggorengan');
                    }
                });
            } else {
                $('#id_waktu_penggorengan').empty().attr('disabled', true);
            }
        });
});

// AJAX khusus untuk form Frayer 4 di halaman Proses Frayer qc sistem
$(document).ready(function() {
        // Cascading untuk Produk → Suhu Frayer (form Frayer 4)
        $('#id_produk_f4_tab').change(function() {
            var id_produk = $(this).val();
            if (id_produk) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('qc-sistem/ajax/get-suhu-frayer-4-by-produk') }}/" + id_produk,
                    success: function(data) {
                        $('#id_suhu_frayer_f4_tab').empty().removeAttr('disabled');
                        $('#id_suhu_frayer_f4_tab').append('<option value="">Pilih Suhu Frayer</option>');
                        $.each(data, function(key, value) {
                            $('#id_suhu_frayer_f4_tab').append('<option value="' + value.id + '">' + value.display+ '</option>');
                        });
                        
                        // Reset waktu penggorengan
                        $('#id_waktu_penggorengan_f4_tab').empty().attr('disabled', true);
                        $('#id_waktu_penggorengan_f4_tab').append('<option value="">Pilih Waktu Penggorengan</option>');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Suhu Frayer:', error);
                        alert('Gagal memuat data Suhu Frayer');
                    }
                });
            } else {
                $('#id_suhu_frayer_f4_tab').empty().attr('disabled', true);
                $('#id_waktu_penggorengan_f4_tab').empty().attr('disabled', true);
            }
        });

        // Cascading untuk Suhu Frayer → Waktu Penggorengan (form Frayer 4)
        $('#id_suhu_frayer_f4_tab').change(function() {
            var id_suhu = $(this).val();
            if (id_suhu) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('qc-sistem/ajax/get-waktu-penggorengan-4-by-suhu') }}/" + id_suhu,
                    success: function(data) {
                        $('#id_waktu_penggorengan_f4_tab').empty().removeAttr('disabled');
                        $('#id_waktu_penggorengan_f4_tab').append('<option value="">Pilih Waktu Penggorengan</option>');
                        $.each(data, function(key, value) {
                            $('#id_waktu_penggorengan_f4_tab').append('<option value="' + value.id + '">' + value.waktu_penggorengan + ' detik</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Waktu Penggorengan:', error);
                        alert('Gagal memuat data Waktu Penggorengan');
                    }
                });
            } else {
                $('#id_waktu_penggorengan_f4_tab').empty().attr('disabled', true);
            }
        });
});
// AJAX khusus untuk form Frayer 5 di halaman Proses Frayer qc sistem
$(document).ready(function() {
    // Frayer 5 - Product change handler
    $('#id_produk_f5_tab').change(function() {
        var productId = $(this).val();
        var suhuSelect = $('#id_suhu_frayer_f5_tab');
        var waktuSelect = $('#id_waktu_penggorengan_f5_tab');
        
        // Clear dependent dropdowns
        suhuSelect.empty().append('<option value="">Pilih Suhu Frayer</option>');
        waktuSelect.empty().append('<option value="">Pilih Waktu Penggorengan</option>');
        
        if (productId) {
            $.ajax({
                url: "{{ url('qc-sistem/ajax/get-suhu-frayer-5-by-produk') }}/" + productId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $.each(data, function(key, value) {
                        suhuSelect.append('<option value="' + value.id + '">' + value.display + '</option>');
                    });
                }
            });
        }
    });
    
    // Frayer 5 - Suhu change handler
    $('#id_suhu_frayer_f5_tab').change(function() {
        var suhuId = $(this).val();
        var waktuSelect = $('#id_waktu_penggorengan_f5_tab');
        
        // Clear waktu dropdown
        waktuSelect.empty().append('<option value="">Pilih Waktu Penggorengan</option>');
        
        if (suhuId) {
            $.ajax({
                url: '{{ url("qc-sistem/ajax/get-waktu-penggorengan-5-by-suhu") }}/' + suhuId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $.each(data, function(key, value) {
                        waktuSelect.append('<option value="' + value.id + '">' + value.waktu_penggorengan + ' detik</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                }
            });
        }
    });
});
// Hasil Penggorengan Cascading Dropdown - FIXED
$(document).ready(function() {
    // HANYA untuk halaman hasil penggorengan, BUKAN hasil proses roasting
    if (window.location.pathname.includes('hasil-penggorengan') && 
        !window.location.pathname.includes('hasil-proses-roasting')) {
        
        $('#id_produk').change(function() {
            var id_produk = $(this).val();
            if (id_produk) {
                let url = "{{ route('get-std-suhu-pusat-by-produk', ['id_produk' => ':id_produk']) }}";
                url = url.replace(':id_produk', id_produk);

                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(data) {
                        $('#id_std_suhu_pusat').empty().removeAttr('disabled');
                        $('#id_std_suhu_pusat').append('<option value="">Pilih Std Suhu Pusat</option>');
                        $.each(data, function(key, value) {
                            $('#id_std_suhu_pusat').append('<option value="' + value.id + '">' + value.std_suhu_pusat + '°C</option>');
                        });
                    }
                });
            } else {
                $('#id_std_suhu_pusat').empty().attr('disabled', true);
                $('#id_std_suhu_pusat').append('<option value="">Pilih Std Suhu Pusat</option>');
            }
        });
    }
});
// Hasil Proses Roasting Cascading Dropdown - FIXED
$(document).ready(function() {
    if (window.location.pathname.includes('hasil-proses-roasting/create')) {
        
        function loadStdSuhuPusatRoasting() {
            var planId = $('input[name="id_plan"]').val() || $('#id_plan_select').val();
            var produkId = $('#id_produk').val();
            
            console.log('Loading for Roasting - Plan:', planId, 'Produk:', produkId);
            
            if (planId && produkId) {
                // GUNAKAN ROUTE HELPER LARAVEL
                var url = "{{ url('qc-sistem/get-std-suhu-pusat-roasting') }}/" + produkId + "/" + planId;
                console.log('AJAX URL:', url); // DEBUG
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log('Roasting AJAX Success:', data);
                        $('#id_std_suhu_pusat').empty().append('<option value="">Pilih Std Suhu Pusat</option>');
                        
                        if (data && data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#id_std_suhu_pusat').append('<option value="' + value.id + '">' + value.std_suhu_pusat_roasting + '°C</option>');
                            });
                            $('#id_std_suhu_pusat').prop('disabled', false);
                        } else {
                            $('#id_std_suhu_pusat').append('<option value="">Tidak ada data untuk produk ini</option>');
                            $('#id_std_suhu_pusat').prop('disabled', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Roasting AJAX Error:', error);
                        $('#id_std_suhu_pusat').empty().append('<option value="">Error loading data</option>');
                        $('#id_std_suhu_pusat').prop('disabled', true);
                    }
                });
            } else {
                $('#id_std_suhu_pusat').empty().append('<option value="">Pilih Std Suhu Pusat</option>').prop('disabled', true);
                console.log('Plan ID or Produk ID is missing');
            }
        }
        
        $('#id_produk').change(function() {
            loadStdSuhuPusatRoasting();
        });
        
        if ($('#id_produk').val()) {
            loadStdSuhuPusatRoasting();
        }
    }
    
    // AJAX untuk halaman edit
    if (window.location.pathname.includes('hasil-proses-roasting') && window.location.pathname.includes('/edit')) {
        
        function loadStdSuhuPusatRoastingEdit() {
            var produkId = $('#id_produk').val();
            
            console.log('Loading for Roasting Edit - Produk:', produkId);
            
            if (produkId) {
                var url = "{{ url('qc-sistem/get-std-suhu-pusat-roasting') }}/" + produkId;
                console.log('AJAX URL Edit:', url);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log('Roasting Edit AJAX Success:', data);
                        $('#id_std_suhu_pusat').empty().append('<option value="">Pilih Std Suhu Pusat</option>');
                        
                        if (data && data.length > 0) {
                            var selectedValue = $('#id_std_suhu_pusat').data('selected');
                            $.each(data, function(key, value) {
                                var selected = (selectedValue == value.id) ? 'selected' : '';
                                $('#id_std_suhu_pusat').append('<option value="' + value.id + '" ' + selected + '>' + value.std_suhu_pusat_roasting + '°C</option>');
                            });
                        } else {
                            $('#id_std_suhu_pusat').append('<option value="">Tidak ada data untuk produk ini</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Roasting Edit AJAX Error:', error);
                    }
                });
            }
        }
        
        // Store selected value before loading
        var selectedStdSuhuPusat = $('#id_std_suhu_pusat').val();
        $('#id_std_suhu_pusat').data('selected', selectedStdSuhuPusat);
        
        $('#id_produk').change(function() {
            loadStdSuhuPusatRoastingEdit();
        });
        
        if ($('#id_produk').val()) {
            loadStdSuhuPusatRoastingEdit();
        }
    }
});

// AJAX proses tumbling QC Sistem - Dinonaktifkan karena bentrok dengan Wizard
$(document).ready(function() {
    // Initialize datetimepicker
    if (typeof $.fn.datetimepicker === 'function' && $('#tanggal').length) {
        $('#tanggal').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        });
    }

    if ($('#tanggal').length && !$('#tanggal').val()) {
        $('#tanggal').val(moment().format('DD-MM-YYYY HH:mm:ss'));
    }
});


$(document).ready(function() {
    // Fungsi ini akan dipakai bersama oleh halaman create dan edit
    function fetchMarinades(produkID, selectedMarinadeID) {
        var $marinadeSelect = $('#id_jenis_marinade');
        if ($marinadeSelect.length === 0) return; // Keluar jika elemen tidak ada

        $marinadeSelect.empty().append('<option value="">-- Memuat... --</option>');

        if (produkID) {
            $.ajax({
                url: '{{ route("get-jenis-marinade-by-produk") }}',
                type: "GET",
                data: { id_produk: produkID },
                dataType: "json",
                success: function(data) {
                    $marinadeSelect.empty().append('<option value="">-- Pilih Jenis Marinade --</option>');
                    if (Object.keys(data).length > 0) {
                        $.each(data, function(key, value) {
                            $marinadeSelect.append('<option value="' + key + '" ' + (key == selectedMarinadeID ? 'selected' : '') + '>' + value + '</option>');
                        });
                    } else {
                        $marinadeSelect.append('<option value="">-- Tidak ada data --</option>');
                    }
                    // Jika ada nilai yang perlu dipilih, pastikan terpilih
                    if (selectedMarinadeID) {
                        $marinadeSelect.val(selectedMarinadeID);
                    }
                },
                error: function() {
                    console.error('AJAX request gagal.');
                    $marinadeSelect.empty().append('<option value="">-- Gagal memuat data --</option>');
                }
            });
        } else {
            $marinadeSelect.empty().append('<option value="">-- Pilih Produk Terlebih Dahulu --</option>');
        }
    }

    // Event handler untuk dropdown produk (berlaku untuk create dan edit)
    $('#id_produk').on('change', function() {
        var produkID = $(this).val();
        fetchMarinades(produkID, null);
    });

    // Logika KHUSUS untuk halaman EDIT
    // Cek jika kita berada di halaman edit dengan memeriksa keberadaan variabel
    @if(isset($prosesMarinadeModel))
        var initialProdukID = '{{ $prosesMarinadeModel->jenisMarinade->id_produk ?? '' }}';
        var initialMarinadeID = '{{ $prosesMarinadeModel->id_jenis_marinade ?? '' }}';
        
        if (initialProdukID) {
            fetchMarinades(initialProdukID, initialMarinadeID);
        }
    @endif
});

// ---BAGIAN AMBIL NILAI BERAT FORM PENGEMASAN PRODUK---
$('#id_pengemasan_produk').change(function() {
    var idBerat = $(this).val();
    
    var berat = '';
   @if(isset($pengemasanProduks))
    var pengemasanProduks = @json($pengemasanProduks);
@else
    var pengemasanProduks = [];
@endif
    
     // Cari berat berdasarkan idBerat
    if (idBerat && pengemasanProduks) {
        var found = pengemasanProduks.find(function(item) {
            return item.id == idBerat;
        });
        if (found && found.berat) {
            berat = found.berat;
        }
    }
   
    $('#berat_pengemasan_produk').val(berat);
});


// Form Edit Pengemasan Produk

$(document).ready(function() {
    // Set berat saat halaman edit dibuka
    var selected = $('#id_pengemasan_produk_for_edit').find(':selected');
    $('#berat_pengemasan_produk_for_edit').val(selected.data('berat') || '');

    // Update berat saat select berubah
    $('#id_pengemasan_produk_for_edit').on('change', function() {
        var berat = $(this).find(':selected').data('berat') || '';
        $('#berat_pengemasan_produk_for_edit').val(berat);
    });
});
// End


// --- BAGIAN FORM BAG ---
// Handler untuk form pertama (backward compatibility)
$('#id_produk_bag').change(function() {
    var id_produk = $(this).val();
    var id_pengemasan_produk = $(this).find(':selected').data('id') || '';
    var berat = '';
    var new_produk = $(this).find(':selected').data('produk-id') || '';
   
    $('#id_produk_value_bag').val(id_pengemasan_produk);
  
    if (id_produk) {
        $.ajax({
            url: "{{ route('get-data-bag-by-produk', '') }}/" + new_produk,
            type: "GET",
            dataType: "json",
            success: function(data) {
                var select = $('#id_data_bag');
                select.empty().append('<option value="">Pilih Nilai Standar Pack</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.std_bag +'</option>');
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                alert('Terjadi kesalahan saat memuat data Bag');
            }
        });
    } else {
        $('#id_data_bag').empty().append('<option value="">Pilih Nilai Standar Pack</option>');
    }
});

// Handler untuk dynamic forms (form ke-2 dan seterusnya)
$(document).on('change', '.produk-select', function() {
    // Skip jika ini adalah form pertama yang sudah ditangani di atas
    if ($(this).attr('id') === 'id_produk_bag' && $(this).closest('.dynamic-form-item').data('index') === 0) {
        return;
    }
    
    var $this = $(this);
    var id_produk = $this.val();
    var id_pengemasan_produk = $this.find(':selected').data('id') || '';
    var new_produk = $this.find(':selected').data('produk-id') || '';
    
    // Find the closest form item to get the related fields
    var $formItem = $this.closest('.dynamic-form-item');
    var $hiddenInput = $formItem.find('input[name="id_pengemasan_produk[]"]');
    var $nilaiStandarSelect = $formItem.find('.nilai-standar-select');
    
    // Set hidden input value
    if ($hiddenInput.length) {
        $hiddenInput.val(id_pengemasan_produk);
    }
  
    if (id_produk && new_produk) {
        $.ajax({
            url: "{{ route('get-data-bag-by-produk', '') }}/" + new_produk,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if ($nilaiStandarSelect.length) {
                    $nilaiStandarSelect.empty().append('<option value="">Pilih Nilai Standar Pack</option>');
                    $.each(data, function(key, value) {
                        $nilaiStandarSelect.append('<option value="' + value.id + '">' + value.std_bag + '</option>');
                    });
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                if ($nilaiStandarSelect.length) {
                    $nilaiStandarSelect.empty().append('<option value="">Error loading data</option>');
                }
            }
        });
    } else {
        if ($nilaiStandarSelect.length) {
            $nilaiStandarSelect.empty().append('<option value="">Pilih Nilai Standar Pack</option>');
        }
    }
});

// --- Khusus Kode Produksi: Fungsi Reusable ---
/**
 * Fungsi untuk parse kode produksi dan generate tanggal expired
 * Format kode: [Huruf1][Huruf2][Angka1][Angka2]
 * - Huruf1: Tahun (P=2026, Q=2027, dst)
 * - Huruf2: Bulan (A=01, B=02, ..., J=10, K=11, L=12)
 * - Angka1-2: Tanggal (01-31)
 * 
 * @param {string} kodeProduksi - Kode produksi yang akan diparse
 * @return {object} - Object dengan format {year, month, day, formatted}
 */
function parseKodeProduksi(kodeProduksi) {
    var result = {
        year: null,
        month: null,
        day: null,
        formatted: null
    };
    
    if (!kodeProduksi || kodeProduksi.length === 0) {
        return result;
    }
    
    var value = kodeProduksi.toUpperCase();
    
    // Hitung tahun berdasarkan huruf pertama (P=2026, Q=2027, R=2028, dst)
    var firstChar = value.charAt(0);
    var baseYear = 2026; // P = 2026
    var baseCharCode = 'P'.charCodeAt(0); // 80
    var currentCharCode = firstChar.charCodeAt(0);
    var yearOffset = currentCharCode - baseCharCode;
    var targetYear = baseYear + yearOffset;
    result.year = targetYear;
    
    // Hitung bulan berdasarkan huruf kedua (A=01, B=02, ..., J=10, K=11, L=12)
    var targetMonth = '01'; // default Januari
    if (value.length >= 2) {
        var secondChar = value.charAt(1);
        var monthCharCode = secondChar.charCodeAt(0);
        var baseMonthCharCode = 'A'.charCodeAt(0); // 65
        var monthNumber = monthCharCode - baseMonthCharCode + 1;
        
        // Batasi bulan antara 1-12
        if (monthNumber >= 1 && monthNumber <= 12) {
            targetMonth = monthNumber.toString().padStart(2, '0');
        }
    }
    result.month = targetMonth;
    
    // Hitung tanggal berdasarkan 2 digit angka setelah huruf kedua (karakter ke-3 dan ke-4)
    var targetDay = '01'; // default tanggal 1
    if (value.length >= 4) {
        var dayString = value.substring(2, 4);
        var dayNumber = parseInt(dayString, 10);
        
        // Validasi tanggal antara 1-31
        if (!isNaN(dayNumber) && dayNumber >= 1 && dayNumber <= 31) {
            targetDay = dayNumber.toString().padStart(2, '0');
        }
    }
    result.day = targetDay;
    
    // Format untuk display: DD/MM/YYYY
    result.formattedDisplay = targetDay + '/' + targetMonth + '/' + targetYear;
    
    // Format untuk input type date: YYYY-MM-DD
    result.formatted = targetYear + '-' + targetMonth + '-' + targetDay;
    
    return result;
}

/**
 * Fungsi untuk setup kode produksi field dengan auto-complete
 * @param {string} kodeProduksiSelector - Selector untuk input kode produksi
 * @param {string} tanggalExpiredSelector - Selector untuk input tanggal expired
 */
function setupKodeProduksi(kodeProduksiSelector, tanggalExpiredSelector) {
    // Prevent space key
    $(document).on('keypress keyup paste', kodeProduksiSelector, function(e) {
        if (e.which === 32) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto uppercase, remove spaces, and set tanggal expired
    $(document).on('input', kodeProduksiSelector, function() {
        // Remove spaces and convert to uppercase
        var value = $(this).val().replace(/\s/g, '').toUpperCase();
        $(this).val(value);
        
        // Parse kode produksi dan set tanggal expired
        if (value.length > 0) {
            var parsed = parseKodeProduksi(value);
            var expiredInput = $(tanggalExpiredSelector);
            
            if (expiredInput.length && parsed.formatted) {
                expiredInput.val(parsed.formatted);
            }
        }
    });
}

// Inisialisasi untuk halaman pengemasan produk
setupKodeProduksi('#kode_produksi', '#tanggal_expired');


// --- BAGIAN FORM BOX ---
$('#id_produk_box').change(function() {
    var id_produk = $(this).val();
     var id_pengemasan_produk = $(this).find(':selected').data('pengemasan-produk-id') || '';
     var id_pengemasan_plastik = $(this).find(':selected').data('pengemasan-plastik-id') || '';
    var new_id_produk = $(this).find(':selected').data('produk-id') || '';
        $('#id_pengemasan_produk').val(id_pengemasan_produk);
        $('#id_pengemasan_plastik').val(id_pengemasan_plastik);
    if (id_produk) {
        $.ajax({
            url: "{{ route('get-data-box-by-produk', '') }}/" + new_id_produk,
            type: "GET",
            dataType: "json",
            success: function(data) {
                var select = $('#id_data_box');
                select.empty().append('<option value="" selected disabled>Pilih Data Box</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.std_box + '</option>');
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                alert('Terjadi kesalahan saat memuat data Box');
            }
        });
    } else {
        $('#id_data_box').empty().append('<option value="" selected disabled>Pilih Data Box</option>');
    }
});

// BAGIAN PENGEMASAN KARTON
$('#id_produk_berat_produk_box').change(function() {
   
     var id_pengemasan_produk = $(this).find(':selected').data('pengemasan-produk-id') || '';
     var id_pengemasan_plastik = $(this).find(':selected').data('pengemasan-plastik-id') || '';
     var id_berat_produk_box =$(this).val();
    var id_berat_produk_pack = $(this).find(':selected').data('berat-produk-pack') || '';
   
    $('#id_berat_produk_box').val(id_berat_produk_box);
    $('#id_berat_produk_bag').val(id_berat_produk_pack);
        $('#id_pengemasan_produk').val(id_pengemasan_produk);
        $('#id_pengemasan_plastik').val(id_pengemasan_plastik);
    
});
//END

// BAGIAN DOKUMENTASI
$('#id_produk_pengemasan_karton').change(function() {
   
     var id_pengemasan_produk = $(this).find(':selected').data('pengemasan-produk-id') || '';
     var id_pengemasan_plastik = $(this).find(':selected').data('pengemasan-plastik-id') || '';
     var id_berat_produk_box = $(this).find(':selected').data('berat-produk-box') || '';
    var id_berat_produk_pack = $(this).find(':selected').data('berat-produk-pack') || '';
   
  
    $('#id_berat_produk_box').val(id_berat_produk_box);
    $('#id_berat_produk_bag').val(id_berat_produk_pack);
        $('#id_pengemasan_produk').val(id_pengemasan_produk);
        $('#id_pengemasan_plastik').val(id_pengemasan_plastik);
    
});
//END

// ajax by std fan roasting fan qc sistem
$(document).ready(function() {
    // Function to handle product selection change using event delegation to ensure it works even if triggered early
    $(document).on('change', '.product-selector', function() {
        const productId = $(this).val();
        const targetTable = $($(this).data('target-table'));
        const formBlock = $(this).closest('.tab-pane').attr('id');
        const isBlok2 = formBlock === 'custom-tabs-one-blok2';
        const suhuRoastingField = isBlok2 ? 'suhu_roasting_2' : 'suhu_roasting';
        const fan1Field = isBlok2 ? 'fan_1_2' : 'fan_1';
        const fan2Field = isBlok2 ? 'fan_2_2' : 'fan_2';
        const aktualLamaProsesField = isBlok2 ? 'aktual_lama_proses_2' : 'aktual_lama_proses';
        // New dynamic field names for blok 1 & 2 tabs
        const fan3Field = isBlok2 ? 'fan_3_2' : 'fan_3';
        const fan4Field = isBlok2 ? 'fan_4_2' : 'fan_4';
        const aktualHumadityField = isBlok2 ? 'aktual_humadity_2' : 'aktual_humadity';
        const infraRedField = isBlok2 ? 'infra_red_2' : 'infra_red';

        // Clear and rebuild the entire table
        if (productId) {
            // Show loading state
            targetTable.html(`
                <thead class="thead-dark">
                    <tr>
                        <th class="text-left align-middle font-weight-bold" style="width: 250px; background-color: #343a40; color: white;">
                            <strong>II. PROSES ROASTING/ STEAMING</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>Blok 4</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>Blok 3</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>Blok 2</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>Blok 1</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>Parameter Pemasakan</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>INFEED</strong>
                        </th>
                        <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                            <strong>OUTFEED</strong>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" class="text-center">Memuat data...</td></tr>
                </tbody>
            `);

            // Get suhu blok data
            $.get("{{ url('qc-sistem/get-suhu-blok-by-produk') }}/" + productId, function(suhuBlokData) {
                console.log('Suhu Blok Data:', suhuBlokData);
                
                if (suhuBlokData && suhuBlokData.length > 0) {
                    // Organize data by blocks - populate all 4 blocks with the same data
                    let blokData = {1: null, 2: null, 3: null, 4: null};
                    let fanData = {1: null, 2: null, 3: null, 4: null};
                    let requestsCompleted = 0;
                    const totalRequests = suhuBlokData.length;
                    
                    // Use the first suhu blok data for all blocks
                    const firstSuhuBlok = suhuBlokData[0];
                    
                    // Populate all blocks with the same data
                    for (let i = 1; i <= 4; i++) {
                        blokData[i] = firstSuhuBlok;
                    }
                    
                    // Get fan data for the first suhu blok and apply to all blocks
                    $.get("{{ url('qc-sistem/get-fan-by-suhu') }}/" + firstSuhuBlok.id, function(response) {
                        if (response && response.success) {
                            // Apply the same fan data to all blocks
                            for (let i = 1; i <= 4; i++) {
                                fanData[i] = response;
                            }
                        }
                        
                        // Build table with populated data for all blocks
                        buildBlockTable(targetTable, blokData, fanData, suhuRoastingField, fan1Field, fan2Field, aktualLamaProsesField, fan3Field, fan4Field, aktualHumadityField, infraRedField);
                        
                    }).fail(function() {
                        // Even if fan data fails, still build table with suhu data
                        buildBlockTable(targetTable, blokData, fanData, suhuRoastingField, fan1Field, fan2Field, aktualLamaProsesField);
                    });
                } else {
                    targetTable.find('tbody').html('<tr><td colspan="8" class="text-center">Tidak ada data suhu blok untuk produk ini</td></tr>');
                }
            }).fail(function() {
                targetTable.find('tbody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data suhu blok</td></tr>');
            });
        } else {
            // Reset to original table structure
            targetTable.html(`
                <thead>
                    <tr>
                        <th>Suhu Blok (Standart)</th>
                        <th>Fan 1 (Standart)</th>
                        <th>Fan 2 (Standart)</th>
                        <th>Suhu Roasting (Aktual)</th>
                        <th>Fan 1 (Aktual)</th>
                        <th>Fan 2 (Aktual)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" class="text-center">Pilih produk terlebih dahulu</td></tr>
                </tbody>
            `);
        }
    });

    // Function to build the block-based table
    function buildBlockTable(targetTable, blokData, fanData, suhuRoastingField, fan1Field, fan2Field, aktualLamaProsesField, fan3Field, fan4Field, aktualHumadityField, infraRedField) {
        let tableBody = '';
        
        // Row 1: Standar Suhu Roasting/Steaming
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standart Suhu Pemasakan (°C)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[4]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[4]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[4]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="4">
                    <input type="hidden" name="block_label[]" value="Blok 4">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[3]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[3]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[3]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="3">
                    <input type="hidden" name="block_label[]" value="Blok 3">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[2]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[2]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[2]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="2">
                    <input type="hidden" name="block_label[]" value="Blok 2">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[1]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[1]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[1]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="1">
                    <input type="hidden" name="block_label[]" value="Blok 1">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[1]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[1]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[1]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="ParameterPemasakan">
                    <input type="hidden" name="block_label[]" value="ParameterPemasakan">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[1]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[1]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[1]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="INFEED">
                    <input type="hidden" name="block_label[]" value="Infeed">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${blokData[1]?.suhu_blok || ''}" readonly>
                    <input type="hidden" name="id_suhu_blok[]" value="${blokData[1]?.id || ''}">
                    <input type="hidden" name="id_std_fan[]" value="${fanData[1]?.id || ''}">
                    <input type="hidden" name="block_number[]" value="OUTFEED">
                    <input type="hidden" name="block_label[]" value="Outfeed">
                </td>
            </tr>
        `;
        
        // Row 2: Suhu Roasting/Steaming (Aktual)
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Suhu Pemasakan (Setting/Aktual) (°C)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${suhuRoastingField}[]">
                </td>
            </tr>
        `;
        
        // Row 3: Standar Fan 1
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standar Fan 1 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[4]?.std_fan || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[3]?.std_fan || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[2]?.std_fan || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan || ''}" readonly>
                </td>
            </tr>
        `;
        
        // Row 4: Fan 1 (Aktual)
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Fan 1 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan1Field}[]">
                </td>
            </tr>
        `;
        
        // Row 5: Standar Fan 2
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standar Fan 2 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[4]?.std_fan_2 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[3]?.std_fan_2 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[2]?.std_fan_2 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan_2 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan_2 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan_2 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_fan_2 || ''}" readonly>
                </td>
            </tr>
        `;
        
        // Row 6: Fan 2 (Aktual)
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Fan 2 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan2Field}[]">
                </td>
            </tr>
        `;
        
        // Row 7: Standar Fan 3
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standar Fan 3 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[4]?.fan_3 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[3]?.fan_3 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[2]?.fan_3 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_3 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_3 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_3 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_3 || ''}" readonly>
                </td>
            </tr>
        `;
        
        // Row 8: Fan 3 (Aktual)
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Fan 3 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan3Field}[]">
                </td>
            </tr>
        `;
        
        // Row 9: Standar Fan 4
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standar Fan 4 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[4]?.fan_4 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[3]?.fan_4 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[2]?.fan_4 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_4 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_4 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_4 || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.fan_4 || ''}" readonly>
                </td>
            </tr>
        `;
        
        // Row 10: Fan 4 (Aktual)
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Fan 4 (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${fan4Field}[]">
                </td>
            </tr>
        `;
        
        // Row 11: Standar Humadity
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standart Humidity/Steam Valve (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[4]?.std_humadity || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[3]?.std_humadity || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[2]?.std_humadity || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_humadity || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_humadity || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_humadity || ''}" readonly>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_humadity || ''}" readonly>
                </td>
            </tr>
        `;
        
        // Row 12: Humadity (Aktual) - Merged for INFEED & OUTFEED
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Aktual Humidity/Steam Valve (%)
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualHumadityField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualHumadityField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualHumadityField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualHumadityField}[]">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualHumadityField}[]">
                </td>
                <td class="text-center" colspan="2">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualHumadityField}_infeed_outfeed" placeholder="Infeed & Outfeed">
                </td>
            </tr>
        `;
        
        // Row 13: Infra Red (Aktual) - Merged for INFEED & OUTFEED
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Infra Red
                </td>
                <td class="text-center">
                    <select class="form-control form-control-sm text-center" name="${infraRedField}[]">
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="form-control form-control-sm text-center" name="${infraRedField}[]">
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="form-control form-control-sm text-center" name="${infraRedField}[]">
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="form-control form-control-sm text-center" name="${infraRedField}[]">
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="form-control form-control-sm text-center" name="${infraRedField}[]">
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </td>
                <td class="text-center" colspan="2">
                    <select class="form-control form-control-sm text-center" name="${infraRedField}_infeed_outfeed">
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </td>
            </tr>
        `;
        
        // Row 14: Standar Lama Proses
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Standar Lama Proses (Menit)
                </td>
                <td class="text-center" colspan="7">
                    <input type="text" class="form-control form-control-sm text-center" value="${fanData[1]?.std_lama_proses || '-'}" readonly>
                </td>
            </tr>
        `;
        
        // Row 15: Lama Proses (Aktual)
        tableBody += `
            <tr>
                <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                    Lama Proses (Display Aktual; Menit)
                </td>
                <td class="text-center" colspan="7">
                    <input type="text" class="form-control form-control-sm text-center" name="${aktualLamaProsesField}" placeholder="/">
                </td>
            </tr>
        `;
        // Update the table body
        targetTable.find('tbody').html(tableBody);
    }
});

// =========================
// PEMASAKAN NASI MODULE
// =========================
$(document).ready(function() {
    var isPemasakanNasiPage = window.location.pathname.includes('/qc-sistem/pemasakan-nasi');
    
    if (isPemasakanNasiPage) {
        // Status Cooking Switch
        $('#status_cooking').change(function() {
            var label = $('#status_cooking_label');
            if ($(this).is(':checked')) {
                label.text('Aktif');
            } else {
                label.text('Tidak Aktif');
            }
        });
        
        // Dynamic Forms - Jenis Bahan & Jumlah
        $(document).on('click', '.add-bahan', function() {
            var newRow = `
                <div class="row bahan-row mb-2">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Jenis Bahan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jenis_bahan[]" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="jumlah[]" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm remove-bahan">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#bahan-container').append(newRow);
        });
        
        $(document).on('click', '.remove-bahan', function() {
            $(this).closest('.bahan-row').remove();
        });
    }
});

// Pemeriksaan Rheon Machine
$(document).ready(function() {
    // Only run on pemeriksaan rice bites or rheon machine pages
    if (window.location.pathname.includes('pemeriksaan-rheon-machine')) {
        console.log('Pemeriksaan Rice Bites/Rheon Machine page loaded');

        function renderStdBeratInfo(data) {
            var $el = $('#std-berat-rheon-info');
            if (!$el.length) {
                return;
            }

            if (!data || !data.found) {
                $el.text('Standar berat belum diatur untuk produk ini.').show();
                return;
            }

            var parts = [];
            if (data.std_adonan) parts.push('Std Adonan: ' + data.std_adonan);
            if (data.std_filler) parts.push('Std Filler: ' + data.std_filler);
            if (data.std_after_forming) parts.push('Std After Forming: ' + data.std_after_forming);
            if (data.std_after_frying) parts.push('Std After Frying: ' + data.std_after_frying);

            $el.text(parts.length ? parts.join(' | ') : 'Standar berat belum diatur untuk produk ini.').show();
        }

        function loadStdBeratInfo() {
            var produkId = $('#id_produk').val();
            var $el = $('#std-berat-rheon-info');
            if ($el.length) {
                $el.hide().text('');
            }

            if (!produkId) {
                return;
            }

            var url = "{{ route('pemeriksaan-rheon-machine.std-berat', ['id_produk' => '__ID__']) }}";
            url = url.replace('__ID__', produkId);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    renderStdBeratInfo(resp);
                },
                error: function() {
                    if ($el.length) {
                        $el.text('Gagal memuat standar berat.').show();
                    }
                }
            });
        }

        $(document).on('change', '#id_produk', function() {
            loadStdBeratInfo();
        });

        if ($('#id_produk').val()) {
            loadStdBeratInfo();
        }
        
        var sectionCounter = 1;
        var afterSectionCounter = 1;
        var afterFormingCount = 1;
        var afterFryingCount = 1;

        // Update remove buttons visibility
        function updateRemoveButtons() {
            var sections = $('.berat-section');
            if (sections.length > 1) {
                $('.remove-berat-section').show();
            } else {
                $('.remove-berat-section').hide();
            }
        }

        // Update after remove buttons visibility
        function updateAfterRemoveButtons() {
            var afterSections = $('.after-forming-frying-section');
            if (afterSections.length > 1) {
                $('.remove-after-berat-section').show();
            } else {
                $('.remove-after-berat-section').hide();
            }
        }

        // Add new berat section
        $('#add-berat-section').click(function(e) {
            e.preventDefault();
            console.log('Add berat section clicked');
            
            sectionCounter++;
            var newSection = `
                <div class="berat-section mb-4" data-section="${sectionCounter}">
                    <div class="row">
                        <!-- Dough/Adonan Column -->
                        <div class="col-md-6 mb-4">
                            
                            <!-- Input and Controls Row -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="flex-fill mr-3">
                                    <small class="text-muted d-block mb-1">berat</small>
                                    <input type="text" class="form-control form-control-sm input-dough-berat" 
                                           placeholder="opsional: isi berat (default: 1)" style="border-radius: 20px; font-size: 12px;">
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-center mr-4">
                                        <h6 class="font-weight-bold text-primary mb-1">dough/adonan</h6>
                                        <small class="text-muted">jumlah item : <span class="count-dough font-weight-bold text-primary">0</span></small>
                                    </div>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-dough" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle remove-dough" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="dough-items-container">
                                <!-- Items will be added dynamically -->
                            </div>
                        </div>

                        <!-- Filler Column -->
                        <div class="col-md-6 mb-4">
                            <!-- Controls Row -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                
                                <div class="d-flex align-items-center">
                                    <div class="text-center mr-4">
                                        <h6 class="font-weight-bold text-success mb-1">filler isi</h6>
                                        <small class="text-muted">jumlah item : <span class="count-filler font-weight-bold text-success">0</span></small>
                                    </div>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-filler" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle remove-filler" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="filler-items-container">
                                <!-- Items will be added dynamically -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remove Section Button -->
                    <div class="text-right mb-3">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-berat-section">
                            <i class="fas fa-trash mr-1"></i> Hapus Section
                        </button>
                    </div>
                </div>
            `;
            
            $('#berat-sections-container').append(newSection);
            updateRemoveButtons();
            calculateTotals();
        });

        // Remove berat section
        $(document).on('click', '.remove-berat-section', function(e) {
            e.preventDefault();
            $(this).closest('.berat-section').remove();
            updateRemoveButtons();
            calculateTotals();
        });

        // Dough/Adonan handlers (delegated events)
        $(document).on('click', '.add-dough', function(e) {
            e.preventDefault();
            var section = $(this).closest('.berat-section');
            var sectionNum = section.data('section');
            var sectionNumwithValue = section.data('section');
            var inputValue = parseFloat(section.find('.input-dough-berat').val()) || 0;
            var container = section.find('.dough-items-container');
            var counter = section.find('.count-dough');
            var currentCount = parseInt(counter.text());
            
            // Always add item with default value of 1, regardless of input value
            // Input value is NOT included in calculation, only used for display/reference
            container.append(`<input type="hidden" name="berat_dough_adonan_items[${sectionNum}][]" class="dough-item" value="1" readonly>
            <input type="hidden" name="input_dough_berat_with_value[${sectionNumwithValue}][]" class="dough-item-value" value="${inputValue}" readonly>
            `);
            counter.text(currentCount + 1);
            calculateTotals();
        });

        $(document).on('click', '.remove-dough', function(e) {
            e.preventDefault();
            var section = $(this).closest('.berat-section');
            var container = section.find('.dough-items-container');
            var counter = section.find('.count-dough');
            var currentCount = parseInt(counter.text());
            
            if (currentCount > 0) {
                // Remove both the count item and value item (remove last 2 inputs)
                container.find('.dough-item-value:last').remove();
                container.find('.dough-item:last').remove();
                counter.text(currentCount - 1);
                calculateTotals();
            }
        });

        // Filler handlers (delegated events)
        $(document).on('click', '.add-filler', function(e) {
            e.preventDefault();
            var section = $(this).closest('.berat-section');
            var sectionNum = section.data('section');
            var container = section.find('.filler-items-container');
            var inputValue = parseFloat(section.find('.input-dough-berat').val()) || 0;
            var counter = section.find('.count-filler');
            var currentCount = parseInt(counter.text());
            
            // Add default value of 1 for filler (since there's no input field)
            container.append(`<input type="hidden" name="berat_filler_items[${sectionNum}][]" class="filler-item" value="1">
            <input type="hidden" name="berat_filler_items_with_value[${sectionNum}][]" class="filler-item-value" value="${inputValue}">`);
            
            counter.text(currentCount + 1);
            calculateTotals();
        });

        $(document).on('click', '.remove-filler', function(e) {
            e.preventDefault();
            var section = $(this).closest('.berat-section');
            var container = section.find('.filler-items-container');
            var counter = section.find('.count-filler');
            var currentCount = parseInt(counter.text());
            
            if (currentCount > 0) {
                container.find('.filler-item:last').remove();
                counter.text(currentCount - 1);
                calculateTotals();
            }
        });

        // After Forming handlers
        $('#add-after-forming').click(function() {
            var inputValue = parseFloat($('#input-after-forming-berat').val()) || 0;
            if (inputValue > 0) {
                afterFormingCount++;
                $('#after-forming-items-container').append('<input type="hidden" name="berat_after_forming_items[]" class="after-forming-item" value="' + inputValue + '">');
                $('#count-after-forming').text(afterFormingCount);
                $('#input-after-forming-berat').val('');
                calculateAfterTotals();
            }
        });

        $('#remove-after-forming').click(function() {
            if (afterFormingCount > 1) {
                $('#after-forming-items-container .after-forming-item:last').remove();
                afterFormingCount--;
                $('#count-after-forming').text(afterFormingCount);
                calculateAfterTotals();
            }
        });

        // After Frying handlers
        $('#add-after-frying').click(function() {
            var inputValue = parseFloat($('#input-after-frying-berat').val()) || 0;
            if (inputValue > 0) {
                afterFryingCount++;
                $('#after-frying-items-container').append('<input type="hidden" name="berat_after_frying_items[]" class="after-frying-item" value="' + inputValue + '">');
                $('#count-after-frying').text(afterFryingCount);
                $('#input-after-frying-berat').val('');
                calculateAfterTotals();
            }
        });

        $('#remove-after-frying').click(function() {
            if (afterFryingCount > 1) {
                $('#after-frying-items-container .after-frying-item:last').remove();
                afterFryingCount--;
                $('#count-after-frying').text(afterFryingCount);
                calculateAfterTotals();
            }
        });

        // Form validation - exclude delete forms, export forms, modal forms, and search/filter forms
        $('form:not(.delete-form):not(.no-validation)').on('submit', function(e) {
            // Skip validation if form is inside a modal, or is an export/filter form
            var actionUrl = $(this).attr('action') || '';
            if ($(this).closest('.modal').length > 0 || actionUrl.indexOf('export') !== -1 || $(this).hasClass('export-form')) {
                return true;
            }

            var isValid = true;
            var errorMessage = '';

            var form = $(this);
            var tanggalInput = form.find('#tanggal, input[name="tanggal"]');
            var shiftInput = form.find('#shift_id, select[name="shift_id"], select[name="shift"]');
            var produkInput = form.find('#id_produk, select[name="id_produk"], select[name="produk"]');

            if (tanggalInput.length > 0 && tanggalInput.prop('required') && !tanggalInput.val()) {
                isValid = false;
                errorMessage += '- Tanggal harus diisi\n';
            }
            if (shiftInput.length > 0 && shiftInput.prop('required') && !shiftInput.val()) {
                isValid = false;
                errorMessage += '- Shift harus dipilih\n';
            }
            if (produkInput.length > 0 && produkInput.prop('required') && !produkInput.val()) {
                isValid = false;
                errorMessage += '- Produk harus dipilih\n';
            }

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi data berikut:\n\n' + errorMessage);
                return false;
            }
        });

        // After Forming handlers (delegated events)
        $(document).on('click', '.add-after-forming', function(e) {
            e.preventDefault();
            var section = $(this).closest('.after-forming-frying-section');
            var sectionNum = section.data('section');
            var container = section.find('.after-forming-items-container');
            var inputValue = parseFloat(section.find('.input-after-forming-berat').val()) || 0;
            var counter = section.find('.count-after-forming');
            var currentCount = parseInt(counter.text());
            
            // Always add item with default value of 1, regardless of input value
            container.append(`<input type="hidden" name="berat_after_forming_items[${sectionNum}][]" class="after-forming-item" value="1">
            <input type="hidden" name="berat_after_forming_items_with_value[${sectionNum}][]" class="after-forming-item-value" value="${inputValue}">`);
            counter.text(currentCount + 1);
            calculateAfterTotals();
        });

        $(document).on('click', '.remove-after-forming', function(e) {
            e.preventDefault();
            var section = $(this).closest('.after-forming-frying-section');
            var container = section.find('.after-forming-items-container');
            var counter = section.find('.count-after-forming');
            var currentCount = parseInt(counter.text());
            
            if (currentCount > 0) {
                container.find('.after-forming-item:last').remove();
                counter.text(currentCount - 1);
                calculateAfterTotals();
            }
        });

        // After Frying handlers (delegated events)
        $(document).on('click', '.add-after-frying', function(e) {
            e.preventDefault();
            var section = $(this).closest('.after-forming-frying-section');
            var sectionNum = section.data('section');
            var container = section.find('.after-frying-items-container');
            var inputValue = parseFloat(section.find('.input-after-forming-berat').val()) || 0;
            var counter = section.find('.count-after-frying');
            var currentCount = parseInt(counter.text());
            
            // Add default value of 1 for frying (since there's no input field)
            container.append(`<input type="hidden" name="berat_after_frying_items[${sectionNum}][]" class="after-frying-item" value="1">
            <input type="hidden" name="berat_after_frying_items_with_value[${sectionNum}][]" class="after-frying-item-value" value="${inputValue}">`);
            counter.text(currentCount + 1);
            calculateAfterTotals();
        });

        $(document).on('click', '.remove-after-frying', function(e) {
            e.preventDefault();
            var section = $(this).closest('.after-forming-frying-section');
            var container = section.find('.after-frying-items-container');
            var counter = section.find('.count-after-frying');
            var currentCount = parseInt(counter.text());
            
            if (currentCount > 0) {
                container.find('.after-frying-item:last').remove();
                counter.text(currentCount - 1);
                calculateAfterTotals();
            }
        });

        // Add new after forming/frying section
        $('#add-after-berat-section').click(function(e) {
            e.preventDefault();
           
            
            afterSectionCounter++;
            var newSection = `
                <div class="after-forming-frying-section mb-4" data-section="${afterSectionCounter}">
                    <div class="row">
                        <!-- After Forming Column -->
                        <div class="col-md-6 mb-4">
                            <!-- Input and Controls Row -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="flex-fill mr-3">
                                    <small class="text-muted d-block mb-1">berat</small>
                                    <input type="text" class="form-control form-control-sm input-after-forming-berat" 
                                           placeholder="opsional: isi berat (default: 1)" style="border-radius: 20px; font-size: 12px;">
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-center mr-4">
                                        <h6 class="font-weight-bold text-warning mb-1">after forming</h6>
                                        <small class="text-muted">jumlah item : <span class="count-after-forming font-weight-bold text-warning">0</span></small>
                                    </div>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-after-forming" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle remove-after-forming" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="after-forming-items-container">
                                <!-- Items will be added dynamically -->
                            </div>
                        </div>

                        <!-- After Frying Column -->
                        <div class="col-md-6 mb-4">
                            <!-- Controls Row -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                
                                <div class="d-flex align-items-center">
                                    <div class="text-center mr-4">
                                        <h6 class="font-weight-bold text-danger mb-1">after frying</h6>
                                        <small class="text-muted">jumlah item : <span class="count-after-frying font-weight-bold text-danger">0</span></small>
                                    </div>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-after-frying" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle remove-after-frying" 
                                                style="width: 28px; height: 28px; font-size: 12px;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="after-frying-items-container">
                                <!-- Items will be added dynamically -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remove Section Button -->
                    <div class="text-right mb-3">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-after-berat-section">
                            <i class="fas fa-trash mr-1"></i> Hapus Section
                        </button>
                    </div>
                </div>
            `;
            
            $('#add-after-berat-section').before(newSection);
            updateAfterRemoveButtons();
        });

        // Remove after forming/frying section
        $(document).on('click', '.remove-after-berat-section', function(e) {
            e.preventDefault();
            $(this).closest('.after-forming-frying-section').remove();
            updateAfterRemoveButtons();
            calculateAfterTotals();
        });

        // Pre-populate data for edit mode
        if ($('meta[name="edit-mode"]').length > 0) {
         
            
            // Get existing data from meta tags
            var existingDoughData = JSON.parse($('meta[name="existing-dough-data"]').attr('content') || '[]');
            var existingFillerData = JSON.parse($('meta[name="existing-filler-data"]').attr('content') || '[]');
            var existingAfterFormingData = JSON.parse($('meta[name="existing-after-forming-data"]').attr('content') || '[]');
            var existingAfterFryingData = JSON.parse($('meta[name="existing-after-frying-data"]').attr('content') || '[]');
            
            console.log('Existing data:', {
                dough: existingDoughData,
                filler: existingFillerData,
                afterForming: existingAfterFormingData,
                afterFrying: existingAfterFryingData
            });
            
            // Pre-populate dough/filler sections
            populateBeratSections(existingDoughData, existingFillerData);
            
            // Pre-populate after forming/frying sections
            populateAfterSections(existingAfterFormingData, existingAfterFryingData);
            
            // Recalculate totals
            setTimeout(function() {
                calculateTotals();
                calculateAfterTotals();
            }, 500);
        }

        // Function to populate berat sections with existing data
        function populateBeratSections(doughData, fillerData) {
            var maxSections = Math.max(doughData.length, fillerData.length, 1);
            
            // Add additional sections if needed
            for (var i = 1; i < maxSections; i++) {
                $('#add-berat-section').click();
            }
            
            // Populate each section
            $('.berat-section').each(function(index) {
                var section = $(this);
                
                // Populate dough data
                if (doughData[index] && Array.isArray(doughData[index])) {
                    var doughContainer = section.find('.dough-items-container');
                    var doughCounter = section.find('.count-dough');
                    
                    doughData[index].forEach(function(value) {
                        if (value && value > 0) {
                            doughContainer.append(`<input type="hidden" name="berat_dough_adonan_items[${index + 1}][]" class="dough-item" value="${value}">`);
                        }
                    });
                    doughCounter.text(doughData[index].length);
                }
                
                // Populate filler data
                if (fillerData[index] && Array.isArray(fillerData[index])) {
                    var fillerContainer = section.find('.filler-items-container');
                    var fillerCounter = section.find('.count-filler');
                    
                    fillerData[index].forEach(function(value) {
                        if (value && value > 0) {
                            fillerContainer.append(`<input type="hidden" name="berat_filler_items[${index + 1}][]" class="filler-item" value="${value}">`);
                        }
                    });
                    fillerCounter.text(fillerData[index].length);
                }
            });
        }

        // Function to populate after sections with existing data
        function populateAfterSections(afterFormingData, afterFryingData) {
            var maxSections = Math.max(afterFormingData.length, afterFryingData.length, 1);
            
            // Add additional sections if needed
            for (var i = 1; i < maxSections; i++) {
                $('#add-after-berat-section').click();
            }
            
            // Populate each section
            $('.after-forming-frying-section').each(function(index) {
                var section = $(this);
                
                // Populate after forming data
                if (afterFormingData[index] && Array.isArray(afterFormingData[index])) {
                    var formingContainer = section.find('.after-forming-items-container');
                    var formingCounter = section.find('.count-after-forming');
                    
                    afterFormingData[index].forEach(function(value) {
                        if (value && value > 0) {
                            formingContainer.append(`<input type="hidden" name="berat_after_forming_items[${index + 1}][]" class="after-forming-item" value="${value}">`);
                        }
                    });
                    formingCounter.text(afterFormingData[index].length);
                }
                
                // Populate after frying data
                if (afterFryingData[index] && Array.isArray(afterFryingData[index])) {
                    var fryingContainer = section.find('.after-frying-items-container');
                    var fryingCounter = section.find('.count-after-frying');
                    
                    afterFryingData[index].forEach(function(value) {
                        if (value && value > 0) {
                            fryingContainer.append(`<input type="hidden" name="berat_after_frying_items[${index + 1}][]" class="after-frying-item" value="${value}">`);
                        }
                    });
                    fryingCounter.text(afterFryingData[index].length);
                }
            });
        }

        // Initialize
        updateRemoveButtons();
        updateAfterRemoveButtons();
    }
});

// Calculate totals for Dough/Filler across all sections
function calculateTotals() {
    var doughTotal = 0;
    var fillerTotal = 0;
    var doughCount = 0;
    var fillerCount = 0;

    // Calculate dough total from all sections - ONLY from hidden input items that were added, NOT from text input fields
    $('.dough-item').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            doughTotal += value;
            doughCount++;
        }
    });

    // Calculate filler count - ONLY from hidden input items that were added, NOT from text input fields
    $('.filler-item').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            fillerTotal += value;
            fillerCount++;
        }
    });

   

    // New calculation logic:
    // 1. If dough count > 0 and filler count = 0, calculate dough total only
    // 2. If filler count > 0 and dough count = 0, calculate filler total only
    // 3. Otherwise, calculate separately
    
    // Calculate dough average using weighted formula: (input1*count1 + input2*count2 + ...) / total_count
    var doughAverage = 0;
    var weightedSum = 0;
    var totalDoughCount = 0;
    
    // Calculate weighted sum from all sections
    $('.berat-section').each(function() {
        var section = $(this);
        var inputValue = parseFloat(section.find('.input-dough-berat').val()) || 0;
        var sectionCount = parseInt(section.find('.count-dough').text()) || 0;
        
        if (inputValue > 0 && sectionCount > 0) {
            weightedSum += inputValue * sectionCount;
            totalDoughCount += sectionCount;
   
        }
    });
    
    if (weightedSum > 0 && totalDoughCount > 0) {
        // Weighted average calculation: (input1*count1 + input2*count2 + ...) / total_count
        doughAverage = weightedSum / totalDoughCount;
     
    } else {
        // Normal calculation: total / count
        doughAverage = doughCount > 0 ? doughTotal / doughCount : 0;
      
    }

    if (doughCount > 0 && fillerCount === 0) {
        // Calculate dough total only
        $('#jumlah-dough').text(Math.round(doughTotal));
        $('#rata-rata-dough').text(doughAverage.toFixed(2) + ' g');
        $('#jumlah-filler').text('0');
        $('#rata-rata-filler').text('0.00 g');
        
        // Update readonly input fields
        $('#rata_rata_dough').val(doughAverage.toFixed(2));
        $('#rata_rata_filler').val('0.00');
    } else if (fillerCount > 0 && doughCount === 0) {
        // Calculate filler total only
        // Calculate filler average using weighted formula with dough input values
        var fillerAverage = 0;
        var fillerWeightedSum = 0;
        var totalFillerCount = 0;
        
        // Calculate weighted sum from all sections using dough input values
        $('.berat-section').each(function() {
            var section = $(this);
            var inputDoughValue = parseFloat(section.find('.input-dough-berat').val()) || 0;
            var sectionFillerCount = parseInt(section.find('.count-filler').text()) || 0;
            
            if (inputDoughValue > 0 && sectionFillerCount > 0) {
                fillerWeightedSum += inputDoughValue * sectionFillerCount;
                totalFillerCount += sectionFillerCount;
                console.log("Filler Section - DoughInput:", inputDoughValue, "FillerCount:", sectionFillerCount, "Weighted:", inputDoughValue * sectionFillerCount);
            }
        });
        
        if (fillerWeightedSum > 0 && totalFillerCount > 0) {
            // Weighted average calculation using dough input values
            fillerAverage = fillerWeightedSum / totalFillerCount;
            console.log("Weighted filler calculation - WeightedSum:", fillerWeightedSum, "TotalCount:", totalFillerCount, "Average:", fillerAverage);
        } else {
            // Normal calculation: total / count
            fillerAverage = fillerCount > 0 ? fillerTotal / fillerCount : 0;
            console.log("Normal filler calculation - Total:", fillerTotal, "Count:", fillerCount, "Average:", fillerAverage);
        }
        
        $('#jumlah-dough').text('0');
        $('#rata-rata-dough').text('0.00 g');
        $('#jumlah-filler').text(Math.round(fillerTotal));
        $('#rata-rata-filler').text(fillerAverage.toFixed(2) + ' g');
        
        // Update readonly input fields
        $('#rata_rata_dough').val('0.00');
        $('#rata_rata_filler').val(fillerAverage.toFixed(2));
    } else {
        // Normal calculation - separate for each or both zero
        // Calculate filler average using weighted formula with dough input values
        var fillerAverage = 0;
        var fillerWeightedSum = 0;
        var totalFillerCount = 0;
        
        // Calculate weighted sum from all sections using dough input values
        $('.berat-section').each(function() {
            var section = $(this);
            var inputDoughValue = parseFloat(section.find('.input-dough-berat').val()) || 0;
            var sectionFillerCount = parseInt(section.find('.count-filler').text()) || 0;
            
            if (inputDoughValue > 0 && sectionFillerCount > 0) {
                fillerWeightedSum += inputDoughValue * sectionFillerCount;
                totalFillerCount += sectionFillerCount;
                console.log("Filler Section - DoughInput:", inputDoughValue, "FillerCount:", sectionFillerCount, "Weighted:", inputDoughValue * sectionFillerCount);
            }
        });
        
        if (fillerWeightedSum > 0 && totalFillerCount > 0) {
            // Weighted average calculation using dough input values
            fillerAverage = fillerWeightedSum / totalFillerCount;
            console.log("Weighted filler calculation - WeightedSum:", fillerWeightedSum, "TotalCount:", totalFillerCount, "Average:", fillerAverage);
        } else {
            // Normal calculation: total / count
            fillerAverage = fillerCount > 0 ? fillerTotal / fillerCount : 0;
            console.log("Normal filler calculation - Total:", fillerTotal, "Count:", fillerCount, "Average:", fillerAverage);
        }

        $('#jumlah-dough').text(Math.round(doughTotal));
        $('#rata-rata-dough').text(doughAverage.toFixed(2) + ' g');
        $('#jumlah-filler').text(Math.round(fillerTotal));
        $('#rata-rata-filler').text(fillerAverage.toFixed(2) + ' g');
        
        // Update readonly input fields
        $('#rata_rata_dough').val(doughAverage.toFixed(2));
        $('#new_jumlah_dough').val(Math.round(doughTotal));
        $('#new_jumlah_filler').val(Math.round(fillerTotal));
        $('#rata_rata_filler').val(fillerAverage.toFixed(2));
    }
}

// Calculate totals for After Forming/Frying using weighted average concept
function calculateAfterTotals() {
    var formingTotal = 0;
    var fryingTotal = 0;
    var formingCount = 0;
    var fryingCount = 0;

    // Calculate after forming total - ONLY from hidden input items, NOT from text inputs
    $('.after-forming-item').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            formingTotal += value;
            formingCount++;
        }
    });

    // Calculate after frying total - ONLY from hidden input items, NOT from text inputs
    $('.after-frying-item').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            fryingTotal += value;
            fryingCount++;
        }
    });

    console.log("After Forming - Count:", formingCount, "Total:", formingTotal);
    console.log("After Frying - Count:", fryingCount, "Total:", fryingTotal);

    // Calculate weighted average using input values from after forming/frying sections
    var afterAverage = 0;
    var afterWeightedSum = 0;
    var totalAfterCount = 0;
    
    // Calculate weighted sum from all after forming/frying sections
    $('.after-forming-frying-section').each(function() {
        var section = $(this);
        var inputFormingValue = parseFloat(section.find('.input-after-forming-berat').val()) || 0;
        var inputFryingValue = parseFloat(section.find('.input-after-frying-berat').val()) || 0;
        var sectionFormingCount = parseInt(section.find('.count-after-forming').text()) || 0;
        var sectionFryingCount = parseInt(section.find('.count-after-frying').text()) || 0;
        
        // Add weighted contribution from forming
        if (inputFormingValue > 0 && sectionFormingCount > 0) {
            afterWeightedSum += inputFormingValue * sectionFormingCount;
            totalAfterCount += sectionFormingCount;
            console.log("After Forming Section - Input:", inputFormingValue, "Count:", sectionFormingCount, "Weighted:", inputFormingValue * sectionFormingCount);
        }
        
        // Add weighted contribution from frying
        if (inputFryingValue > 0 && sectionFryingCount > 0) {
            afterWeightedSum += inputFryingValue * sectionFryingCount;
            totalAfterCount += sectionFryingCount;
            console.log("After Frying Section - Input:", inputFryingValue, "Count:", sectionFryingCount, "Weighted:", inputFryingValue * sectionFryingCount);
        }
    });
    
    if (afterWeightedSum > 0 && totalAfterCount > 0) {
        // Weighted average calculation: (input1*count1 + input2*count2 + ...) / total_count
        afterAverage = afterWeightedSum / totalAfterCount;
        console.log("Weighted after calculation - WeightedSum:", afterWeightedSum, "TotalCount:", totalAfterCount, "Average:", afterAverage);
    } else {
        // Normal calculation: total / count
        var grandTotal = formingTotal + fryingTotal;
        var totalCount = formingCount + fryingCount;
        afterAverage = totalCount > 0 ? grandTotal / totalCount : 0;
        console.log("Normal after calculation - GrandTotal:", grandTotal, "TotalCount:", totalCount, "Average:", afterAverage);
    }

    // Calculate separate averages for forming and frying
    var formingAverage = 0;
    var fryingAverage = 0;
    var formingWeightedSum = 0;
    var fryingWeightedSum = 0;
    var totalFormingCount = 0;
    var totalFryingCount = 0;
    
    // Calculate weighted averages separately
    $('.after-forming-frying-section').each(function() {
        var section = $(this);
        var inputFormingValue = parseFloat(section.find('.input-after-forming-berat').val()) || 0;
        var sectionFormingCount = parseInt(section.find('.count-after-forming').text()) || 0;
        var sectionFryingCount = parseInt(section.find('.count-after-frying').text()) || 0;
        
        // Calculate forming weighted sum
        if (inputFormingValue > 0 && sectionFormingCount > 0) {
            formingWeightedSum += inputFormingValue * sectionFormingCount;
            totalFormingCount += sectionFormingCount;
        }
        
        // For frying, use input-after-forming-berat value (same as filler logic)
        if (inputFormingValue > 0 && sectionFryingCount > 0) {
            fryingWeightedSum += inputFormingValue * sectionFryingCount;
            totalFryingCount += sectionFryingCount;
            console.log("After Frying Section - FormingInput:", inputFormingValue, "FryingCount:", sectionFryingCount, "Weighted:", inputFormingValue * sectionFryingCount);
        }
    });
    
    // Calculate separate averages
    if (formingWeightedSum > 0 && totalFormingCount > 0) {
        formingAverage = formingWeightedSum / totalFormingCount;
    } else {
        formingAverage = formingCount > 0 ? formingTotal / formingCount : 0;
    }
    
    if (fryingWeightedSum > 0 && totalFryingCount > 0) {
        fryingAverage = fryingWeightedSum / totalFryingCount;
        console.log("Weighted frying calculation - WeightedSum:", fryingWeightedSum, "TotalCount:", totalFryingCount, "Average:", fryingAverage);
    } else {
        fryingAverage = fryingCount > 0 ? fryingTotal / fryingCount : 0;
        console.log("Normal frying calculation - Total:", fryingTotal, "Count:", fryingCount, "Average:", fryingAverage);
    }
    
    // Update separate displays
    $('#jumlah-after-forming').text(Math.round(formingTotal));
    $('#rata-rata-after-forming').text(formingAverage.toFixed(2) + ' g');
    $('#jumlah-after-frying').text(Math.round(fryingTotal));
    $('#rata-rata-after-frying').text(fryingAverage.toFixed(2) + ' g');
    
    // Update readonly input fields - get values from span displays
    var spanFormingValue = $('#rata-rata-after-forming').text().replace(' g', '');
    var spanFryingValue = $('#rata-rata-after-frying').text().replace(' g', '');
    $('#new_jumlah_after_forming').val(Math.round(formingTotal));
    $('#new_jumlah_after_frying').val(Math.round(fryingTotal));
    $('#rata_rata_after_forming').val(spanFormingValue);
    $('#rata_rata_after_frying').val(spanFryingValue);
}

// Store initial values to detect changes
var initialEditValues = {};

// Load initial values from database on page load
function loadInitialEditValues() {
    initialEditValues = {
        doughValues: [],
        fillerValues: [],
        afterFormingValues: [],
        afterFryingValues: []
    };
    
    // Store initial input values
    $('input[name^="input_dough_berat"]').each(function() {
        initialEditValues.doughValues.push(parseFloat($(this).val()) || 0);
    });
    
    $('input[name^="input_filler_berat"]').each(function() {
        initialEditValues.fillerValues.push(parseFloat($(this).val()) || 0);
    });
    
    $('input[name^="input_after_forming_berat"]').each(function() {
        initialEditValues.afterFormingValues.push(parseFloat($(this).val()) || 0);
    });
    
    $('input[name^="input_after_frying_berat"]').each(function() {
        initialEditValues.afterFryingValues.push(parseFloat($(this).val()) || 0);
    });
    
    console.log('Initial values loaded:', initialEditValues);
}

// Check if values have changed from initial state
function hasValuesChanged() {
    var currentValues = {
        doughValues: [],
        fillerValues: [],
        afterFormingValues: [],
        afterFryingValues: []
    };
    
    // Get current input values
    $('input[name^="input_dough_berat"]').each(function() {
        currentValues.doughValues.push(parseFloat($(this).val()) || 0);
    });
    
    $('input[name^="input_filler_berat"]').each(function() {
        currentValues.fillerValues.push(parseFloat($(this).val()) || 0);
    });
    
    $('input[name^="input_after_forming_berat"]').each(function() {
        currentValues.afterFormingValues.push(parseFloat($(this).val()) || 0);
    });
    
    $('input[name^="input_after_frying_berat"]').each(function() {
        currentValues.afterFryingValues.push(parseFloat($(this).val()) || 0);
    });
    
    // Compare arrays
    function arraysEqual(a, b) {
        if (a.length !== b.length) return false;
        for (let i = 0; i < a.length; i++) {
            if (a[i] !== b[i]) return false;
        }
        return true;
    }
    
    var changed = !arraysEqual(currentValues.doughValues, initialEditValues.doughValues) ||
                  !arraysEqual(currentValues.fillerValues, initialEditValues.fillerValues) ||
                  !arraysEqual(currentValues.afterFormingValues, initialEditValues.afterFormingValues) ||
                  !arraysEqual(currentValues.afterFryingValues, initialEditValues.afterFryingValues);
    
    console.log('Values changed:', changed);
    return changed;
}

// Calculate totals specifically for Edit Form with change detection
function calculateEditTotals(forceRecalculate = false) {
    console.log('Calculating edit totals...');
    
    // If initial values haven't been loaded yet, don't proceed
    if (!initialEditValues.doughValues) {
        console.log('Initial values not loaded yet, skipping calculation');
        return;
    }
    
    // If not forced and values haven't changed, don't recalculate
    if (!forceRecalculate && !hasValuesChanged()) {
        console.log('No changes detected, keeping database values');
        return;
    }
    
    console.log('Changes detected or forced recalculation, calculating new values...');
    
    // Initialize totals for counting individual values
    var doughTotal = 0;
    var fillerTotal = 0;
    var afterFormingTotal = 0;
    var afterFryingTotal = 0;
    
    var doughCount = 0;
    var fillerCount = 0;
    var afterFormingCount = 0;
    var afterFryingCount = 0;

    // Count individual input values for totals
    $('input[name^="input_dough_berat"]').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            doughTotal += value;
            doughCount++;
        }
    });

    $('input[name^="input_filler_berat"]').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            fillerTotal += value;
            fillerCount++;
        }
    });

    $('input[name^="input_after_forming_berat"]').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            afterFormingTotal += value;
            afterFormingCount++;
        }
    });

    $('input[name^="input_after_frying_berat"]').each(function() {
        var value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            afterFryingTotal += value;
            afterFryingCount++;
        }
    });

    // Advanced Calculation Logic for Dough/Filler (Cross-reference calculation)
    var doughAverage = 0;
    var fillerAverage = 0;

    if (doughCount > 0 && fillerCount === 0) {
        // Calculate dough average only
        doughAverage = doughTotal / doughCount;
        fillerAverage = 0;
    } else if (fillerCount > 0 && doughCount === 0) {
        // Calculate filler average only
        fillerAverage = fillerTotal / fillerCount;
        doughAverage = 0;
    } else if (doughCount > 0 && fillerCount > 0) {
        // Both exist - calculate separately
        doughAverage = doughTotal / doughCount;
        fillerAverage = fillerTotal / fillerCount;
    } else {
        // Both zero
        doughAverage = 0;
        fillerAverage = 0;
    }

    // Advanced Calculation Logic for After Forming/Frying (Cross-reference calculation)
    var afterFormingAverage = 0;
    var afterFryingAverage = 0;

    if (afterFormingCount > 0 && afterFryingCount === 0) {
        // Calculate after forming average only
        afterFormingAverage = afterFormingTotal / afterFormingCount;
        afterFryingAverage = 0;
    } else if (afterFryingCount > 0 && afterFormingCount === 0) {
        // Calculate after frying average only (using forming input logic)
        afterFryingAverage = afterFryingTotal / afterFryingCount;
        afterFormingAverage = 0;
    } else if (afterFormingCount > 0 && afterFryingCount > 0) {
        // Both exist - calculate separately
        afterFormingAverage = afterFormingTotal / afterFormingCount;
        afterFryingAverage = afterFryingTotal / afterFryingCount;
    } else {
        // Both zero
        afterFormingAverage = 0;
        afterFryingAverage = 0;
    }

    console.log('Recalculated Results:');
    console.log('Dough - Total:', doughTotal, 'Count:', doughCount, 'Average:', doughAverage);
    console.log('Filler - Total:', fillerTotal, 'Count:', fillerCount, 'Average:', fillerAverage);
    console.log('After Forming - Total:', afterFormingTotal, 'Count:', afterFormingCount, 'Average:', afterFormingAverage);
    console.log('After Frying - Total:', afterFryingTotal, 'Count:', afterFryingCount, 'Average:', afterFryingAverage);

    // Update the readonly input fields in the summary section with proper formatting
    // For edit form: Show COUNTS instead of TOTALS for jumlah fields
    $('#edit_rata_rata_dough').val(doughAverage.toFixed(2));
    $('#edit_new_jumlah_dough').val(doughCount); // Count instead of total
    $('#edit_rata_rata_filler').val(fillerAverage.toFixed(2));
    $('#edit_new_jumlah_filler').val(fillerCount); // Count instead of total
    $('#edit_rata_rata_after_forming').val(afterFormingAverage.toFixed(2));
    $('#edit_new_jumlah_after_forming').val(afterFormingCount); // Count instead of total
    $('#edit_rata_rata_after_frying').val(afterFryingAverage.toFixed(2));
    $('#edit_new_jumlah_after_frying').val(afterFryingCount); // Count instead of total
    
    // Update initial values after recalculation
    loadInitialEditValues();
}

// Edit Form JavaScript for Rheon Machine
if (window.location.pathname.includes('pemeriksaan-rheon-machine') && $('meta[name="edit-mode"]').length > 0) {
    // Add new value to existing section
    $('.add-dough-value').click(function() {
        var section = $(this).data('section');
        var container = $(this).parent();
        var newInput = `
            <div class="mr-1 mb-1">
                <input type="number" 
                       name="input_dough_berat[${section}][]" 
                       class="form-control form-control-sm editable-badge badge-primary" 
                       value="1" 
                       style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #007bff; border: 1px solid #007bff;"
                       step="0.01">
            </div>
        `;
        $(this).before(newInput);
        calculateEditTotals(); // Recalculate after adding new input
    });

    $('.add-filler-value').click(function() {
        var section = $(this).data('section');
        var container = $(this).parent();
        var newInput = `
            <div class="mr-1 mb-1">
                <input type="number" 
                       name="input_filler_berat[${section}][]" 
                       class="form-control form-control-sm editable-badge badge-success" 
                       value="1" 
                       style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #28a745; border: 1px solid #28a745;"
                       step="0.01">
            </div>
        `;
        $(this).before(newInput);
        calculateEditTotals(); // Recalculate after adding new input
    });

    $('.add-forming-value').click(function() {
        var section = $(this).data('section');
        var container = $(this).parent();
        var newInput = `
            <div class="mr-1 mb-1">
                <input type="number" 
                       name="input_after_forming_berat[${section}][]" 
                       class="form-control form-control-sm editable-badge badge-warning" 
                       value="1" 
                       style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #ffc107; border: 1px solid #ffc107;"
                       step="0.01">
            </div>
        `;
        $(this).before(newInput);
        calculateEditTotals(); // Recalculate after adding new input
    });

    $('.add-frying-value').click(function() {
        var section = $(this).data('section');
        var container = $(this).parent();
        var newInput = `
            <div class="mr-1 mb-1">
                <input type="number" 
                       name="input_after_frying_berat[${section}][]" 
                       class="form-control form-control-sm editable-badge badge-danger" 
                       value="1" 
                       style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #dc3545; border: 1px solid #dc3545;"
                       step="0.01">
            </div>
        `;
        $(this).before(newInput);
        calculateEditTotals(); // Recalculate after adding new input
    });

    // Add new sections
    $('.add-dough-section').click(function() {
        var sectionCount = $('.card-outline.card-primary').length;
        var newSection = sectionCount + 1;
        // Add logic to create new section
        alert('Tambah Section Dough ' + newSection + ' - Feature coming soon');
    });

    $('.add-filler-section').click(function() {
        var sectionCount = $('.card-outline.card-success').length;
        var newSection = sectionCount + 1;
        // Add logic to create new section
        alert('Tambah Section Filler ' + newSection + ' - Feature coming soon');
    });

    $('.add-forming-section').click(function() {
        var sectionCount = $('.card-outline.card-warning').length;
        var newSection = sectionCount + 1;
        // Add logic to create new section
        alert('Tambah Section After Forming ' + newSection + ' - Feature coming soon');
    });
    $('.add-frying-section').click(function() {
        var sectionCount = $('.card-outline.card-danger').length;
        var newSection = sectionCount + 1;
        // Add logic to create new section
        alert('Tambah Section After Frying ' + newSection + ' - Feature coming soon');
        calculateEditTotals(true); // Force recalculation after adding new input
    });

    // Auto-calculate when values change
    $(document).on('input', '.editable-badge', function() {
        calculateEditTotals();
    });

    // Load initial values and setup
    setTimeout(function() {
        loadInitialEditValues(); // Load initial values first
        // Don't call calculateEditTotals() on initial load to preserve database values
        console.log('Edit form initialized with database values');
    }, 500);
}

// End Ajax Rheon Machine


// AJAX for getting jenis predust by product QC-sistem
function getJenisPredustByProduk(produkId, targetSelectId) {
    if (produkId) {
        $.ajax({
            url: '{{ url("/qc-sistem/ajax/get-jenis-predust-by-produk") }}',
            type: 'GET',
            data: { id_produk: produkId },
            success: function(data) {
                var select = $('#' + targetSelectId);
                select.empty();
                select.append('<option value="">Pilih Jenis Predust</option>');
                
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.jenis_predust + '</option>');
                });
            },
            error: function() {
                console.log('Error loading jenis predust data');
            }
        });
    } else {
        $('#' + targetSelectId).empty().append('<option value="">Pilih Jenis Predust</option>');
    }
}

// search form profile
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    const tableRows = document.querySelectorAll('table tbody tr');
    const tableBody = document.querySelector('table tbody');
    
    if (searchInput && tableRows.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleRows = 0;
            
            // Remove existing "no data" row if present
            const existingNoDataRow = document.querySelector('.no-data-row');
            if (existingNoDataRow) {
                existingNoDataRow.remove();
            }
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    row.style.animation = 'fadeIn 0.3s ease';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show "no data found" message if no rows are visible
            if (visibleRows === 0 && searchTerm.trim() !== '') {
                const noDataRow = document.createElement('tr');
                noDataRow.className = 'no-data-row';
                noDataRow.innerHTML = `
                    <td colspan="5" class="text-center py-4">
                        <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">Tidak ada data yang ditemukan untuk pencarian "<strong>${searchTerm}</strong>"</p>
                        <small class="text-muted">Coba gunakan kata kunci lain</small>
                    </td>
                `;
                tableBody.appendChild(noDataRow);
            }
        });
    }
    
    // Auto dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.querySelector('.close')) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        });
    }, 5000);
});

// Modal Bulk Export PDF - UNIVERSAL VERSION
$(document).ready(function() {
    // Ketika tombol bulk export diklik
    $(document).on('click', '[data-bulk-export="true"]', function(e) {
        e.preventDefault();
        
        // Get the form and route from data attributes
        const form = $(this).closest('.modal').find('form[data-bulk-form="true"]');
        const exportRoute = form.attr('action');
        
        // Tampilkan loading
        $(this).prop('disabled', true).text('Memproses...');
        
        // Create a temporary form for submission
        const tempForm = $('<form>', {
            'method': 'POST',
            'action': exportRoute,
            'target': '_blank'
        });
        
        // Add CSRF token
        tempForm.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        // Add all form data dynamically
        form.find('input, select').each(function() {
            const input = $(this);
            const name = input.attr('name');
            const value = input.val();
            
            if (name && value) {
                tempForm.append($('<input>', {
                    'type': 'hidden',
                    'name': name,
                    'value': value
                }));
            }
        });
        
        // Submit form
        $('body').append(tempForm);
        tempForm.submit();
        tempForm.remove();
        
        // Reset tombol dan tutup modal
        const button = $(this);
        const modal = button.closest('.modal');
        const originalText = button.text().replace('Memproses...', 'Cetak PDF');
        
        setTimeout(() => {
            button.prop('disabled', false).text(originalText);
            modal.modal('hide');
            form[0].reset();
        }, 2000);
    });
});


// Variabel global untuk menyimpan data breader dan tombol yang diklik
var selectedBreaderData = {};
var currentClickedButton = null;

$(document).ready(function() {
    // Ketika tombol "Lanjut ke Frayer" diklik
    $('#lineSelectionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        currentClickedButton = button; // Simpan referensi tombol yang diklik
        
        console.log('Modal opened, button:', button);
        console.log('Button HTML:', button[0].outerHTML);
        
        // Debug semua atribut data
        console.log('All data attributes:');
        $.each(button[0].attributes, function(i, attr) {
            if (attr.name.startsWith('data-')) {
                console.log(attr.name + ': ' + attr.value);
            }
        });
        
        // Ambil data dari atribut
        var breaderUuid = button.attr('data-breader-uuid');
        var produkId = button.attr('data-produk-id');
        var planId = button.attr('data-plan-id');
        var userId = button.attr('data-user-id');
        
        console.log('Raw extracted data:', {
            breaderUuid: breaderUuid,
            produkId: produkId,
            planId: planId,
            userId: userId
        });
        
        // Jika data kosong, coba dengan cara lain
        if (!breaderUuid) {
            console.log('Trying alternative data extraction...');
            breaderUuid = button.data('breaderUuid') || button.data('breader-uuid');
            produkId = button.data('produkId') || button.data('produk-id');
            planId = button.data('planId') || button.data('plan-id');
            userId = button.data('userId') || button.data('user-id');
            
            console.log('Alternative extracted data:', {
                breaderUuid: breaderUuid,
                produkId: produkId,
                planId: planId,
                userId: userId
            });
        }
        
        selectedBreaderData = {
            breaderUuid: breaderUuid,
            produkId: produkId,
            planId: planId,
            userId: userId
        };
        
        console.log('Final selectedBreaderData:', selectedBreaderData);
        
        // Validasi data
        if (!selectedBreaderData.breaderUuid) {
            console.error('Breader UUID tidak ditemukan!');
            alert('Data UUID tidak ditemukan. Silakan refresh halaman dan coba lagi.');
            return false;
        }
    });
    
    // Ketika line dipilih (jQuery event handler)
    $(document).on('click', '.line-btn', function() {
        var selectedLine = $(this).data('line');
        redirectToFrayer(selectedLine);
    });
    
    // Debug: Cek apakah tombol line ada
    console.log('Line buttons found:', $('.line-btn').length);
});

// Fungsi untuk redirect ke halaman Frayer
function redirectToFrayer(lineNumber) {
    console.log('Redirecting to Frayer with line:', lineNumber);
    console.log('Current selectedBreaderData:', selectedBreaderData);
    
    // Validasi data breader - lebih permisif untuk debugging
    if (!selectedBreaderData) {
        console.error('selectedBreaderData is null or undefined');
        alert('Data breader tidak ditemukan. Silakan tutup modal dan coba lagi.');
        return;
    }
    
    // Jika breaderUuid kosong, coba ambil dari tombol yang benar-benar diklik
    if (!selectedBreaderData.breaderUuid && currentClickedButton) {
        console.warn('breaderUuid kosong, menggunakan currentClickedButton...');
        
        var fallbackUuid = currentClickedButton.attr('data-breader-uuid');
        var fallbackProdukId = currentClickedButton.attr('data-produk-id');
        var fallbackPlanId = currentClickedButton.attr('data-plan-id');
        var fallbackUserId = currentClickedButton.attr('data-user-id');
        
        console.log('Using fallback data from currentClickedButton:', {
            fallbackUuid, fallbackProdukId, fallbackPlanId, fallbackUserId
        });
        
        selectedBreaderData = {
            breaderUuid: fallbackUuid,
            produkId: fallbackProdukId,
            planId: fallbackPlanId,
            userId: fallbackUserId
        };
    }
    
    if (!selectedBreaderData.breaderUuid) {
        alert('UUID breader tidak ditemukan. Silakan refresh halaman dan coba lagi.');
        return;
    }
    
    // Tutup modal
    $('#lineSelectionModal').modal('hide');
    
    // Redirect ke halaman proses frayer dengan parameter
    var url = "{{ route('proses-frayer.create') }}" + 
              "?breader_uuid=" + encodeURIComponent(selectedBreaderData.breaderUuid) +
              "&line=" + encodeURIComponent(lineNumber) +
              "&produk_id=" + encodeURIComponent(selectedBreaderData.produkId || '') +
              "&plan_id=" + encodeURIComponent(selectedBreaderData.planId || '') +
              "&user_id=" + encodeURIComponent(selectedBreaderData.userId || '');
    
    console.log('Final redirect URL:', url);
    
    // Redirect langsung
    window.location.href = url;
}

// Fungsi untuk onclick handler
function selectLine(lineNumber) {
    redirectToFrayer(lineNumber);
}

// modal breadering menuju frayer qc sistem
$(document).ready(function() {
    // Cek apakah datang dari breader - gunakan breader_uuid sebagai indikator
    var fromBreader = "{{ request('breader_uuid') }}";
    var selectedLine = "{{ request('line') }}";
    var produkId = "{{ request('produk_id') }}";
    var planId = "{{ request('plan_id') }}";
    var userId = "{{ request('user_id') }}";
    
    // Auto-fill form berdasarkan data dari breader
    if (produkId) {
        $('select[name="id_produk"]').val(produkId).trigger('change');
    }
    if (planId) {
        $('select[name="id_plan"]').val(planId);
    }
    if (userId) {
        $('select[name="user_id"]').val(userId);
    }
    
    // Aktifkan tab sesuai line yang dipilih
    if (selectedLine) {
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('active show');
        
        if (selectedLine === '1') {
            $('a[href="#frayer_1"]').addClass('active');
            $('#frayer_1').addClass('active show');
            $('.line1-continue-btn').show();
        } else if (selectedLine === '2') {
            $('a[href="#frayer_2"]').addClass('active');
            $('#frayer_2').addClass('active show');
            $('.line1-continue-btn').hide();
        } else if (selectedLine === '3') {
            $('a[href="#frayer_4"]').addClass('active');
            $('#frayer_4').addClass('active show');
            $('.line1-continue-btn').hide();
        } else if (selectedLine === '4') {
            $('a[href="#frayer_5"]').addClass('active');
            $('#frayer_5').addClass('active show');
            $('.line1-continue-btn').hide();
        }
    }

    // Handler untuk tombol "Skip Frayer 1" (langsung tampilkan Frayer 2 tanpa simpan Frayer 1)
    $(document).on('click', '#skipFrayer1Btn', function(e) {
        e.preventDefault();

        $('#frayer-2-section').css('display', 'block').show();
        $('#frayer_1 > form').hide();
        $('.line1-continue-btn').hide();
        $(this).hide();
    });
    
    // AJAX handler untuk tombol "Simpan & Lanjut Frayer 2"
    $(document).on('click', '#saveAndContinueBtn', function(e) {
        e.preventDefault();
        console.log('Save and Continue button clicked');
        
        // Validasi: hanya untuk data dari breader
        if (!fromBreader) {
            alert('Form ini hanya dapat digunakan untuk data yang datang dari Breader!');
            return;
        }
        
        // Cek form yang akan disubmit - GUNAKAN SELECTOR YANG TEPAT
        var form = $('#frayer_1 form');
        console.log('Form found:', form.length);
        console.log('Form action:', form.attr('action'));
        
        if (form.length === 0) {
            console.error('Form not found with selector: #frayer_1 form');
            alert('Form tidak ditemukan!');
            return;
        }
        
        // Validasi form
        var formElement = form[0];
        if (!formElement.checkValidity()) {
            formElement.reportValidity();
            return;
        }

        // Hide tombol submit manual Frayer 1 saat proses simpan & lanjut
        $('#submitBtnFrayer1').hide();
        $('#skipFrayer1Btn').hide();
        $('#backBtnFrayer1').hide();
        
        // Disable tombol dan ubah text
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
        
        // Siapkan data form dengan validasi
        var formData = new FormData(formElement);
        formData.append('action', 'save_and_continue');
        
        console.log('Sending AJAX to:', form.attr('action'));
        console.log('Form data prepared');
        
        // AJAX request dengan error handling yang lebih baik
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                
                if (response.success) {
                    // Set frayer_uuid di form Frayer 2
                    if (response.frayer1_uuid) {
                        $('input[name="frayer_uuid"]').val(response.frayer1_uuid);
                        console.log('Frayer UUID set to:', response.frayer1_uuid);
                    }
                    
                    // Tampilkan form Frayer 2
                    showFrayer2Form();
                    
                    // Show success message
                    alert(response.message || 'Data Frayer 1 berhasil disimpan!');
                } else {
                    alert('Terjadi kesalahan: ' + (response.message || 'Unknown error'));

                    // Jika response tidak success, tampilkan lagi tombol submit
                    $('#submitBtnFrayer1').show();
                    $('#skipFrayer1Btn').show();
                    $('#backBtnFrayer1').show();
                }
                
                // Re-enable tombol
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan & Lanjut Frayer 2');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response Text:', xhr.responseText);
                console.error('Status Code:', xhr.status);

                // Tampilkan lagi tombol submit jika gagal
                $('#submitBtnFrayer1').show();
                $('#skipFrayer1Btn').show();
                $('#backBtnFrayer1').show();
                
                var errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                
                if (xhr.status === 422) {
                    // Validation errors
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorList = [];
                        for (var field in errors) {
                            errorList.push(errors[field].join(', '));
                        }
                        errorMsg = 'Validasi gagal: ' + errorList.join('; ');
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMsg = 'Server Error: ' + xhr.responseText;
                }
                
                alert('Error: ' + errorMsg);
                
                // Re-enable tombol
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan & Lanjut Frayer 2');
            }
        });
    });
    
    // Function untuk menampilkan form Frayer 2
    function showFrayer2Form() {
        console.log('Menampilkan form Frayer 2...');
        
        // Tampilkan section Frayer 2
        $('#frayer-2-section').css('display', 'block').show();
        
        // Sembunyikan tombol "Simpan & Lanjut Frayer 2"
        $('#saveAndContinueBtn').hide();
        
        // Scroll ke form Frayer 2
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $('#frayer-2-section').offset().top - 100
            }, 500);
        }, 300);
        
        console.log('Form Frayer 2 berhasil ditampilkan');
    }

    // ========================================
    // Khusus Pemeriksaan Rice Bites
    // ========================================
    
    // Index Page Functions - Verification & Acknowledgment
    if (window.location.href.indexOf('pemeriksaan-rice-bites') !== -1) {
        
        // QC Verification Function (Index Page)
        window.verifyQC = function(uuid) {
            if (confirm('Apakah Anda yakin ingin memverifikasi data ini sebagai QC?')) {
                // Create form for QC verification
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/') }}/qc-sistem/pemeriksaan-rice-bites/${uuid}/verify-qc`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        };

        // Production Acknowledgment Function (Index Page)
        window.acknowledgeProduksi = function(uuid) {
            if (confirm('Apakah Anda yakin ingin menandai data ini sebagai diketahui oleh Produksi?')) {
                // Create form for Production acknowledgment
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/') }}/qc-sistem/pemeriksaan-rice-bites/${uuid}/acknowledge-produksi`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        };

        // Show Page Functions - Delete Confirmation
        if (window.location.href.indexOf('/show') !== -1 || window.location.href.indexOf('/detail') !== -1) {
            window.confirmDelete = function() {
                if (confirm('Apakah Anda yakin ingin menghapus data pemeriksaan rice bites ini?\n\nData yang sudah dihapus tidak dapat dikembalikan.')) {
                    document.getElementById('delete-form').submit();
                }
            };
        }

        // Create & Edit Page Functions - Dynamic Forms
        if (window.location.href.indexOf('/create') !== -1 || window.location.href.indexOf('/edit') !== -1) {
            $(document).ready(function() {
                // Initialize indexes for dynamic forms
                let bahanBakuIndex = window.location.href.indexOf('/edit') !== -1 ? 
                    $('.bahan-baku-item').length : 1;
                let premixIndex = window.location.href.indexOf('/edit') !== -1 ? 
                    $('.premix-item').length : 1;
                let suhuAdonanIndex = window.location.href.indexOf('/edit') !== -1 ? 
                    $('.suhu-adonan-item').length : 1;
                let suhuPencampuranIndex = window.location.href.indexOf('/edit') !== -1 ? 
                    $('.suhu-pencampuran-item').length : 1;

                // Bahan Baku Functions
                $(document).on('click', '.add-bahan-baku', function() {
                    const newItem = `
                        <div class="row bahan-baku-item">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-wheat mr-1"></i>Bahan Baku</label>
                                    <input type="text" name="bahan_baku[${bahanBakuIndex}][nama]" class="form-control" placeholder="Input bahan" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Berat</label>
                                    <div class="input-group">
                                        <input type="text" name="bahan_baku[${bahanBakuIndex}][berat]" class="form-control" placeholder="0">
                                        <div class="input-group-append"><span class="input-group-text">kg</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Suhu</label>
                                    <div class="input-group">
                                        <input type="text" name="bahan_baku[${bahanBakuIndex}][suhu]" class="form-control" placeholder="0">
                                        <div class="input-group-append"><span class="input-group-text">°C</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kondisi</label>
                                    <input type="text" name="bahan_baku[${bahanBakuIndex}][kondisi]" class="form-control" placeholder="Kondisi">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-bahan-baku"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#bahan-baku-container').append(newItem);
                    bahanBakuIndex++;
                });

                $(document).on('click', '.remove-bahan-baku', function() {
                    if ($('.bahan-baku-item').length > 1) {
                        $(this).closest('.bahan-baku-item').remove();
                    } else {
                        alert('Minimal harus ada satu data bahan baku');
                    }
                });

                // Premix Functions
                $(document).on('click', '.add-premix', function() {
                    const newItem = `
                        <div class="row premix-item">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-vial mr-1"></i>Premix</label>
                                    <input type="text" name="premix[${premixIndex}][nama]" class="form-control" placeholder="Input premix" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Berat</label>
                                    <div class="input-group">
                                        <input type="text" name="premix[${premixIndex}][berat]" class="form-control" placeholder="0">
                                        <div class="input-group-append"><span class="input-group-text">kg</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kondisi</label>
                                    <input type="text" name="premix[${premixIndex}][kondisi]" class="form-control" placeholder="kondisi">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-premix"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#premix-container').append(newItem);
                    premixIndex++;
                });

                $(document).on('click', '.remove-premix', function() {
                    if ($('.premix-item').length > 1) {
                        $(this).closest('.premix-item').remove();
                    } else {
                        alert('Minimal harus ada satu data premix');
                    }
                });

                // Suhu Adonan Functions
                $(document).on('click', '.add-suhu-adonan', function() {
                    if ($('.suhu-adonan-item').length >= 3) {
                        alert('Maksimal hanya 3 titik pengukuran suhu adonan');
                        return;
                    }

                    const newItem = `
                        <div class="row suhu-adonan-item">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label><i class="fas fa-thermometer-half mr-1"></i>Suhu Aktual Adonan Titik ${suhuAdonanIndex + 1}</label>
                                    <div class="input-group">
                                        <input type="text" name="suhu_aktual_adonan[${suhuAdonanIndex}]" class="form-control" placeholder="0">
                                        <div class="input-group-append"><span class="input-group-text">°C</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-suhu-adonan"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#suhu-adonan-container').append(newItem);
                    suhuAdonanIndex++;
                    
                    if ($('.suhu-adonan-item').length >= 3) {
                        $('.add-suhu-adonan').hide();
                    }
                });

                $(document).on('click', '.remove-suhu-adonan', function() {
                    $(this).closest('.suhu-adonan-item').remove();
                    
                    if ($('.suhu-adonan-item').length < 3) {
                        $('.add-suhu-adonan').show();
                    }
                    
                    $('.suhu-adonan-item').each(function(index) {
                        $(this).find('label').html(`<i class="fas fa-thermometer-half mr-1"></i>Suhu Aktual Adonan Titik ${index + 1}`);
                        $(this).find('input').attr('name', `suhu_aktual_adonan[${index}]`);
                    });
                });

                // Suhu Pencampuran Functions
                $(document).on('click', '.add-suhu-pencampuran', function() {
                    if ($('.suhu-pencampuran-item').length >= 6) {
                        alert('Maksimal hanya 6 pengukuran suhu adonan setelah pencampuran');
                        return;
                    }

                    const newItem = `
                        <div class="row suhu-pencampuran-item">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label><i class="fas fa-temperature-high mr-1"></i>Suhu Adonan Setelah Pencampuran ${suhuPencampuranIndex + 1}</label>
                                    <div class="input-group">
                                        <input type="text" name="suhu_adonan_pencampuran[${suhuPencampuranIndex}]" class="form-control" placeholder="0">
                                        <div class="input-group-append"><span class="input-group-text">°C</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-suhu-pencampuran"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#suhu-pencampuran-container').append(newItem);
                    suhuPencampuranIndex++;
                    
                    if ($('.suhu-pencampuran-item').length >= 6) {
                        $('.add-suhu-pencampuran').hide();
                    }
                });

                $(document).on('click', '.remove-suhu-pencampuran', function() {
                    $(this).closest('.suhu-pencampuran-item').remove();
                    
                    if ($('.suhu-pencampuran-item').length < 6) {
                        $('.add-suhu-pencampuran').show();
                    }
                    
                    $('.suhu-pencampuran-item').each(function(index) {
                        $(this).find('label').html(`<i class="fas fa-temperature-high mr-1"></i>Suhu Adonan Setelah Pencampuran ${index + 1}`);
                        $(this).find('input').attr('name', `suhu_adonan_pencampuran[${index}]`);
                    });
                });

                // Calculate average temperature from suhu pencampuran
                function calculateRataRataSuhu() {
                    let total = 0;
                    let count = 0;
                    
                    $('.suhu-pencampuran-item input[name^="suhu_adonan_pencampuran"]').each(function() {
                        let value = parseFloat($(this).val());
                        if (!isNaN(value) && value > 0) {
                            total += value;
                            count++;
                        }
                    });
                    
                    let average = count > 0 ? (total / count).toFixed(2) : 0;
                    $('#rata_rata_suhu').val(average);
                }

                // Auto-calculate when suhu pencampuran values change
                $(document).on('input', 'input[name^="suhu_adonan_pencampuran"]', function() {
                    calculateRataRataSuhu();
                });

                // Recalculate when fields are added or removed
                $(document).on('click', '.add-suhu-pencampuran', function() {
                    setTimeout(calculateRataRataSuhu, 100);
                });

                $(document).on('click', '.remove-suhu-pencampuran', function() {
                    setTimeout(calculateRataRataSuhu, 100);
                });

                // Calculate initial average on page load (for edit page)
                if (window.location.href.indexOf('/edit') !== -1) {
                    calculateRataRataSuhu();
                }

                console.log('Pemeriksaan Rice Bites form loaded with dynamic fields');
            });
        }
    }
    // ========================================
    // End Khusus Pemeriksaan Rice Bites
    // ========================================
});
document.addEventListener('DOMContentLoaded', function() {
    let barangIndex = 1;
    
    // Tambah Barang baru
    var addBarangBtn = document.getElementById('addBarang');
    if (!addBarangBtn) {
        return;
    }
    addBarangBtn.addEventListener('click', function() {
        const container = document.getElementById('barang-container');
        const newBarang = `
            <div class="barang-item mb-3" data-index="${barangIndex}">
                <div class="row">
                    <div class="col-md-10">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang[]" class="form-control" placeholder="Masukkan Nama Barang" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-barang">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newBarang);
        barangIndex++;
        updateRemoveButtons();
    });
    
    // Hapus Barang
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-barang') || e.target.closest('.remove-barang')) {
            const barangItem = e.target.closest('.barang-item');
            barangItem.remove();
            updateRemoveButtons();
        }
    });
    
    // Update status tombol hapus
    function updateRemoveButtons() {
        const barangItems = document.querySelectorAll('.barang-item');
        const removeButtons = document.querySelectorAll('.remove-barang');
        
        removeButtons.forEach(button => {
            button.disabled = barangItems.length <= 1;
        });
    }
});

// ========================================
// Khusus Standar Suhu Pusat
// ========================================
$(document).ready(function() {
    let fryerIndex = 1;
    const maxFryers = 10;
    
    // Tambah Fryer
    $('#add-fryer').click(function() {
        if ($('.fryer-row').length >= maxFryers) {
            alert('Maksimal ' + maxFryers + ' Fryer!');
            return;
        }
        
        fryerIndex++;
        let newRow = `
            <div class="row fryer-row mb-2" data-index="${fryerIndex}">
                <div class="col-md-2">
                    <label>Fryer ${fryerIndex}</label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="std_suhu_pusat[]" class="form-control" placeholder="76-80">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-fryer">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;
        
        $('#fryer-container').append(newRow);
        updateFryerLabels();
    });
    
    // Hapus Fryer
    $(document).on('click', '.remove-fryer', function() {
        if ($('.fryer-row').length > 1) {
            $(this).closest('.fryer-row').remove();
            updateFryerLabels();
        }
    });
    
    // Update label dan enable/disable tombol hapus
    function updateFryerLabels() {
        $('.fryer-row').each(function(index) {
            $(this).find('label').html('Fryer ' + (index + 1) + (index === 0 ? ' <span class="text-danger">*</span>' : ''));
            $(this).find('.remove-fryer').prop('disabled', index === 0);
        });
    }

    //Edit Produk 
// Global function untuk dynamic dropdown Plan -> Produk
window.loadProdukByPlan = function(planSelectId, produkSelectId, currentProdukId = null) {
    const $planSelect = $('#' + planSelectId);
    const $produkSelect = $('#' + produkSelectId);
    
    // Load products when page loads
    if ($planSelect.length && $produkSelect.length) {
        loadProducts($planSelect.val());
        
        // Load products when plan changes
        $planSelect.on('change', function() {
            loadProducts($(this).val());
        });
    }
    
    function loadProducts(planId) {
        if (!planId) {
            $produkSelect.html('<option selected disabled>Pilih Produk</option>');
            return;
        }
        
       $.get(`{{ url('super-admin/jenis-emulsi/produk-by-plan') }}/${planId}`)
            .done(function(data) {
                console.log('Data produk:', data);
                
                // Clear existing options except the first one
                $produkSelect.html('<option selected disabled>Pilih Produk</option>');
                
                if (data && data.length > 0) {
                    // Add new options dengan handling field yang berbeda
                    $.each(data, function(index, produk) {
                        // Handle berbagai kemungkinan nama field
                        const namaProduk = produk.nama_produk || produk.nama_produk || 
                                         produk.nama || produk.name || 
                                         'Produk ' + produk.id;
                        
                        const option = $('<option>')
                            .val(produk.id)
                            .text(namaProduk);
                        
                        // Select current product if it matches
                        if (currentProdukId && produk.id == currentProdukId) {
                            option.prop('selected', true);
                        }
                        
                        $produkSelect.append(option);
                    });
                } else {
                    $produkSelect.html('<option selected disabled>Tidak ada produk</option>');
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Error loading products:', error);
                console.error('Response:', xhr.responseText);
                $produkSelect.html('<option selected disabled>Gagal memuat data</option>');
            });
    }
};

// Auto-initialize system untuk dynamic dropdown Plan -> Produk
window.autoInitProdukDropdown = function() {
    const configs = [
        // Format: [path_pattern, plan_select_id, produk_select_id, current_produk_id]
        [/\/jenis-emulsi\/.*\/edit$/, 'id_plan_select', 'id_produk_select', '{{ $item->id_produk ?? "" }}'],
        [/\/total-pemakaian-emulsi\/.*\/edit$/, 'id_plan_select', 'id_produk_select', '{{ $item->id_produk ?? "" }}'],      
        [/\/std-salinitas-viskositas\/.*\/edit$/, 'id_plan', 'id_produk_select', '{{ $item->id_produk ?? "" }}'],      
         // Tambahkan konfigurasi baru di sini:
        // [/\/nama-lain\/.*\/edit$/, 'id_plan_select_lain', 'id_produk_select_lain', '{{ $item->id_produk_lain ?? "" }}'],
        // [/\/create$/, 'id_plan_create', 'id_produk_create', null],
    ];
    
    configs.forEach(config => {
        const [pattern, planId, produkId, currentId] = config;
        if (window.location.pathname.match(pattern)) {
            window.loadProdukByPlan(planId, produkId, currentId);
        }
    });
};

// Jalankan auto-initialization
window.autoInitProdukDropdown();

});

// Global function untuk dynamic dropdown Plan -> Produk -> Better
window.loadBetterByPlanProduk = function(planSelectId, produkSelectId, betterSelectId, currentBetterId = null) {
    const $planSelect = $('#' + planSelectId);
    const $produkSelect = $('#' + produkSelectId);
    const $betterSelect = $('#' + betterSelectId);
    
   // Load better when page loads
    if ($planSelect.length && $produkSelect.length && $betterSelect.length) {
        // Load better setelah produk ter-load
        setTimeout(function() {
            loadBetters($produkSelect.val());
        }, 500); // Tunggu sebentar agar produk ter-select dulu
        
        // Load better when produk changes
        $produkSelect.on('change', function() {
            loadBetters($(this).val());
        });
    }
    
    function loadBetters(produkId) {
        if (!produkId) {
            $betterSelect.html('<option selected disabled>Pilih Better</option>');
            return;
        }
        
      $.get(`{{ url('super-admin/std-salinitas-viskositas/better-by-produk') }}/${produkId}`)
            .done(function(data) {
                console.log('Data better:', data);
                
                $betterSelect.html('<option selected disabled>Pilih Better</option>');
                
                if (data && data.length > 0) {
                    $.each(data, function(index, better) {
                        const option = $('<option>')
                            .val(better.id)
                            .text(better.nama_better);
                        
                        if (currentBetterId && better.id == currentBetterId) {
                            option.prop('selected', true);
                        }
                        
                        $betterSelect.append(option);
                    });
                } else {
                    $betterSelect.html('<option selected disabled>Tidak ada better</option>');
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Error loading betters:', error);
                $betterSelect.html('<option selected disabled>Gagal memuat data</option>');
            });
    }
};

// Auto-initialize untuk std-salinitas-viskositas
    if (window.location.pathname.match(/\/std-salinitas-viskositas\/.*\/edit$/)) {
        window.loadBetterByPlanProduk('id_plan', 'id_produk_select', 'id_better_select', '{{ $item->id_better ?? "" }}');
    }

    // Session Timeout Warning (Simple Version)
    @auth
        $(document).ready(function() {
            let idleTime = 0;
            const sessionLifetimeMinutes = {{ config('session.lifetime', 120) }};
            const warningThresholdSeconds = (sessionLifetimeMinutes - 5) * 60; // Tampilkan 5 menit sebelum sesi asli habis
            const checkIntervalSeconds = 30;

            function resetTimer() {
                if ($('#sessionTimeoutModal').is(':visible')) return;
                idleTime = 0;
            }

            $(document).on('mousemove keypress mousedown touchstart', resetTimer);

            setInterval(function() {
                idleTime += checkIntervalSeconds;
                if (idleTime >= warningThresholdSeconds) {
                    if (!$('#sessionTimeoutModal').is(':visible')) {
                        $('#sessionTimeoutModal').modal('show');
                    }
                }
            }, checkIntervalSeconds * 1000);

            $('#stayLoggedInBtn').click(function() {
                $.get("{{ route('keep-alive') }}")
                    .done(function() {
                        $('#sessionTimeoutModal').modal('hide');
                        idleTime = 0;
                    })
                    .fail(function() {
                        location.reload();
                    });
            });

            $('#logoutNowBtn').click(function() {
                document.getElementById('logout-form').submit();
            });
        });
    @endauth

    // Global Auto-Uppercase Handler
    $(document).on('input', 'input[type="text"]:not(.no-uppercase), textarea:not(.no-uppercase)', function() {
        let start = this.selectionStart;
        let end = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(start, end);
    });
</script>

<!-- Modal Session Timeout -->
<div class="modal fade" id="sessionTimeoutModal" tabindex="-1" role="dialog" aria-labelledby="sessionTimeoutModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header bg-warning text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="sessionTimeoutModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Konfirmasi Sesi
                </h5>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-user-clock text-warning" style="font-size: 50px;"></i>
                </div>
                <h5>Sesi Anda Hampir Berakhir</h5>
                <p class="text-muted">Anda sudah lama tidak aktif. Apakah Anda ingin tetap berada di website ini?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" id="logoutNowBtn" class="btn btn-outline-danger px-4" style="border-radius: 20px;">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
                <button type="button" id="stayLoggedInBtn" class="btn btn-primary px-4" style="border-radius: 20px;">
                    <i class="fas fa-check mr-1"></i> Ya, Tetap di Sini
                </button>
            </div>
        </div>
    </div>
</div>

</body>
