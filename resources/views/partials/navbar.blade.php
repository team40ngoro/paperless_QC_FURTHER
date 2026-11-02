<!-- Navbar -->
<style>
  /* Sidebar Yellow Scrollbar (Native & AdminLTE OverlayScrollbars) */
  .sidebar, .os-content {
    scrollbar-width: thin !important;
    scrollbar-color: #ffc107 rgba(0, 0, 0, 0.2) !important;
  }
  
  /* Target for Webkit browsers */
  .sidebar::-webkit-scrollbar, 
  .os-content::-webkit-scrollbar {
    width: 6px !important;
    display: block !important;
  }
  
  .sidebar::-webkit-scrollbar-track,
  .os-content::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1) !important;
  }
  
  .sidebar::-webkit-scrollbar-thumb,
  .os-content::-webkit-scrollbar-thumb {
    background: #ffc107 !important;
    border-radius: 10px !important;
  }

  /* Target for AdminLTE's OverlayScrollbars Plugin */
  .os-theme-light > .os-scrollbar-vertical > .os-scrollbar-track > .os-scrollbar-handle,
  .os-theme-dark > .os-scrollbar-vertical > .os-scrollbar-track > .os-scrollbar-handle,
  .os-scrollbar-handle {
    background: #ffc107 !important;
    opacity: 1 !important; /* Force visible */
    width: 6px !important;
  }

  .sidebar::-webkit-scrollbar-thumb:hover,
  .os-scrollbar-handle:hover {
    background: #e0a800 !important;
  }

  /* Tablet & Mobile Scroll Fix */
  @media (max-width: 991.98px) {
    .main-sidebar {
      overflow-y: hidden !important;
    }
    .sidebar {
      overflow-y: auto !important;
      height: calc(100vh - 57px) !important;
      -webkit-overflow-scrolling: touch;
    }
  }
</style>
<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background: linear-gradient(90deg, #007bff, #0056b3) !important;">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button" style="transition: all 0.3s ease; padding: 12px 15px;">
        <i class="fas fa-bars text-white" style="font-size: 18px;"></i>
      </a>
    </li>
  </ul>

  <!-- User Role & Plan Info -->
  <div class="ml-3 d-none d-md-block">
    <div class="text-uppercase" style="line-height: 1.1;">
      <small class="text-white" style="font-size: 9px; font-weight: 700; letter-spacing: 1px;">Access Level</small>
      <div class="d-flex align-items-center">
        <span class="text-white font-weight-bold mr-2" style="font-size: 15px;">{{ Auth::user()->roleModel->role ?? Auth::user()->role ?? '-' }}</span>
        @if(Auth::user()->id_role == 5)
          <span class="badge badge-success elevation-2" style="font-size: 10px; border: 1px solid rgba(255,255,255,0.2); padding: 2px 8px;">ALL PLAN</span>
        @elseif(!Auth::user()->isManager())
          <span class="badge badge-success elevation-2" style="font-size: 10px; border: 1px solid rgba(255,255,255,0.2); padding: 2px 8px;">{{ Auth::user()->plan->nama_plan ?? '-' }}</span>
        @endif
      </div>
    </div>
  </div>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto mt-2">

    {{-- SWITCH PLAN DROPDOWN (hanya untuk Manager yang punya > 0 allowed plans) --}}
    @if(Auth::user()->isManager() && Auth::user()->allowedPlans()->count() > 0)
    <li class="nav-item dropdown mr-1">
      <a class="nav-link text-white d-flex align-items-center" data-toggle="dropdown" href="#"
         title="Switch Plant" style="transition: all 0.3s ease; padding: 6px 12px;"> 
        <span class="badge badge-warning badge-pill elevation-2 mr-2" style="font-size: 11px; padding: 5px 12px; font-weight: 700; color: #1e293b; letter-spacing: 0.5px;">
          <i class="fas fa-exchange-alt mr-1" style="font-size: 9px;"></i>
          {{ strtoupper(Auth::user()->getEffectivePlan()->nama_plan ?? Auth::user()->plan->nama_plan ?? '-') }}
        </span>
        <i class="fas fa-exchange-alt text-warning mr-1" style="font-size: 15px;"></i> 
        <span class="text-white">Switch Plant</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right p-0" style="border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.15); width:280px; overflow:hidden; border:none;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color:white; padding:12px 16px; font-weight:700; font-size:13px; display:flex; align-items:center;">
          <i class="fas fa-building mr-2" style="font-size:14px;"></i> SWITCH PLANT
        </div>

        <!-- Search Bar Input -->
        <div class="p-2 border-bottom" style="background:#fff;">
          <input type="text" id="search-plant-input" class="form-control form-control-sm" 
                 placeholder="Cari plant..." 
                 style="border-radius:8px; border:1px solid #cbd5e1; padding:6px 12px; font-size:13px;"
                 onclick="event.stopPropagation();">
        </div>

        <!-- Plant List Container -->
        <div id="plant-list-container" style="max-height:260px; overflow-y:auto;">
          @foreach(Auth::user()->allowedPlans()->orderBy('nama_plan')->get() as $switchPlan)
            @php
              $firstLetter = strtoupper(substr($switchPlan->nama_plan, 0, 1));
              $isActive = Auth::user()->active_plan_id == $switchPlan->id;
            @endphp
            <form action="{{ route('manager.switch-plan') }}" method="POST" class="plant-item-form" data-name="{{ strtolower($switchPlan->nama_plan) }}">
              @csrf
              <input type="hidden" name="plan_id" value="{{ $switchPlan->id }}">
              <button type="submit" class="dropdown-item d-flex align-items-center py-2 px-3" 
                      style="border-bottom:1px solid #f1f5f9; transition: background 0.2s ease; {{ $isActive ? 'background-color:#e0f2fe; border-left:4px solid #0284c7;' : '' }}">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center mr-3" 
                     style="width:34px; height:34px; background-color:{{ $isActive ? '#0284c7' : '#94a3b8' }}; font-weight:700; font-size:14px; flex-shrink:0;">
                  {{ $firstLetter }}
                </div>
                <div class="flex-grow-1 text-truncate" style="font-size:13px; color:{{ $isActive ? '#0369a1' : '#334155' }}; font-weight:{{ $isActive ? '700' : '500' }};">
                  {{ $switchPlan->nama_plan }}
                  @if($switchPlan->abbrivation)
                    <small style="color:{{ $isActive ? '#0284c7' : '#64748b' }};">({{ $switchPlan->abbrivation }})</small>
                  @endif
                </div>
                @if($isActive)
                  <i class="fas fa-check text-success ml-2" style="font-size:14px;"></i>
                @endif
              </button>
            </form>
          @endforeach
        </div>

        <!-- Reset Button Footer -->
        @if(Auth::user()->active_plan_id)
          <div class="border-top" style="background:#f8fafc;">
            <form action="{{ route('manager.reset-plan') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item py-2 px-3 text-secondary d-flex align-items-center" style="font-size:12px; font-weight:500;">
                <i class="fas fa-redo-alt mr-2 text-muted" style="font-size:12px;"></i> 
                Kembali ke Plant Asal
              </button>
            </form>
          </div>
        @endif
      </div>
    </li>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        var searchInput = document.getElementById("search-plant-input");
        if (searchInput) {
          searchInput.addEventListener("keyup", function() {
            var filter = this.value.toLowerCase();
            var items = document.querySelectorAll(".plant-item-form");
            items.forEach(function(item) {
              var text = item.getAttribute("data-name") || "";
              if (text.indexOf(filter) > -1) {
                item.style.display = "";
              } else {
                item.style.display = "none";
              }
            });
          });
        }
      });
    </script>
    @endif

    <!-- Settings Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link text-white" data-toggle="dropdown" href="#" style="transition: all 0.3s ease; padding: 12px 15px;">
        <i class="fas fa-cog text-white" style="font-size: 18px;"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); min-width: 280px;">
          <div class="dropdown-item-text text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin: -8px -16px 8px -16px; padding: 20px;">
            <div style="text-transform: uppercase;">
              <strong style="font-size: 16px;">{{ Auth::user()->name ?? 'User' }}</strong>
              <div style="font-size: 12px; opacity: 0.9;">{{ Auth::user()->email ?? '' }}</div>
              <div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">
                <i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i>Online
              </div>
            </div>
          </div>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding: 12px 16px;">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset($assetPath . 'dist/img/cpi-logo.png') }}" alt="Paperless Future Logo" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold" >Paperless Further</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Navbar Menu -->
        <nav class="mt-2 pb-5 mb-5">
          <ul class="nav nav-pills nav-sidebar flex-column pb-5" data-widget="treeview" role="menu" data-accordion="true">
            <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
              <li class="nav-item has-treeview">
                <a href="{{ url('/grafana-dashboard') }}" class="nav-link {{ request()->is('super-admin/dashboard*') || request()->is('dashboard*') || request()->is('/') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p><b>Dashboard</b></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('traceability.index') }}" class="nav-link {{ request()->routeIs('traceability.*') ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-search-location text-info"></i>
                  <p><b>Traceability Batch</b></p>
                </a>
              </li>
            @if(in_array(auth()->user()->id_role, [5, 1, 4])) <!-- Superadmin=5, Admin=1 -->
              <!-- Menu Data Master SUPER ADMIN -->
              @php
                $isDataMasterMenu = request()->is('*roles*')
                  || request()->is('*plan*')
                  || request()->is('*shift*')
                  || request()->is('*data-rm*')
                  || request()->is('*data-seasoning*')
                  || request()->is('*data-defect*')
                  || request()->is('*input-area*')
                  || request()->is('*data-barang*')
                  || request()->routeIs('produk.*')
                  || request()->is('*jenis-better*')
                  || request()->is('*suhu-adonan*')
                  || request()->routeIs('nomor-formula.*')
                  || request()->routeIs('bahan-forming.*')
                  || request()->routeIs('bahan-non-forming.*')
                  || request()->routeIs('bahan-emulsi.*')
                  || request()->is('*jenis-emulsi*')
                  || request()->is('*suhu-frayer-1*')
                  || request()->is('*suhu-frayer-2*')
                  || request()->is('*std-suhu-pusat*')
                  || request()->is('*jenis-breader*')
                  || request()->is('*jenis-predust*')
                  || request()->is('*std-salinitas-viskositas*')
                  || request()->is('*data-tumbling*')
                  || request()->is('*suhu-blok*')
                  || request()->is('*std-fan*')
                  || request()->is('*std-suhu-pusat-roasting*')
                  || request()->is('*nama-formula-fla*')
                  || request()->is('*nomor-step-formula-fla*')
                  || request()->is('*bahan-formula-fla*')
                  || request()->is('*std-berat-rheon*')
                  || request()->is('*bahan-baku-rice-bites*')
                  || request()->is('*data-bag*')
                  || request()->is('*data-box*')
                  || request()->is('*data-timbangan*')
                  || request()->is('*master-cip*')
                  || request()->is('*data-thermo*')
                  || request()->is('*total-pemakaian-emulsi*')
                  || request()->is('*nomor-emulsi*')
                  || request()->is('*input-mesin-peralatan*')
                  || request()->is('*list-peralatan*');
              @endphp
              <li class="nav-item has-treeview {{ $isDataMasterMenu ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ $isDataMasterMenu ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fa fa-briefcase"></i>
                  <p>Data Master 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @if(auth()->user()->id_role == 5) <!-- Hanya Superadmin -->
                  <li class="nav-item">
                    <a href="{{ route ('access-control.index') }}" class="nav-link {{ request()->is('*access-control*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Access Control</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('roles.index') }}" class="nav-link {{ request()->is('*roles*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Role</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('plan.index') }}" class="nav-link {{ request()->is('*plan*') && !request()->is('*assign-plans*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Plant</p>
                    </a>
                  </li>
                  <!-- <li class="nav-item">
                    <a href="{{ route('users.profile') }}" class="nav-link {{ request()->is('*assign-plans*') || request()->is('super-admin/profile') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-exchange-alt nav-icon" style="font-size:8px;"></i>
                      <p> Assign Plan Manager</p>
                    </a>
                  </li> -->
                  @endif
                  <li class="nav-item">
                    <a href="{{ route ('master-standar-audit.index') }}" class="nav-link {{ request()->is('*master-standar-audit*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-sliders-h nav-icon text-warning" style="font-size:10px;"></i>
                      <p> Master Standar Audit</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-shift.index') }}" class="nav-link {{ request()->is('*shift*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Shift</p>
                    </a>
                  </li>
                  
                  <li class="nav-item">
                    <a href="{{ route ('data-rm.index') }}" class="nav-link {{ request()->is('*data-rm*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data RM</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-seasoning.index') }}" class="nav-link {{ request()->is('*data-seasoning*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data Seasoning</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-defect.index') }}" class="nav-link {{ request()->is('*data-defect*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data Defect</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('input-area.index') }}" class="nav-link {{ request()->is('*input-area*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Area</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-barang.index') }}" class="nav-link {{ request()->is('*data-barang*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data Barang</p>
                    </a>
                  </li>
                  
                  <li class="nav-item">
                    <a href="{{ route ('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Jenis Produk</p>
                    </a>
                  </li>
                  
                  <li class="nav-item has-treeview {{ request()->routeIs('nomor-formula.*') || request()->routeIs('bahan-forming.*') || request()->routeIs('bahan-non-forming.*')  ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('nomor-formula.*') || request()->routeIs('bahan-forming.*') || request()->routeIs('bahan-non-forming.*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-code nav-icon"></i>
                      <p>
                        Formula & Bahan
                        <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ route ('nomor-formula.index') }}" class="nav-link {{ request()->routeIs('nomor-formula.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Nomor F. Forming</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('bahan-forming.index')}}" class="nav-link {{ request()->routeIs('bahan-forming.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Bahan Forming</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('bahan-non-forming.index')}}" class="nav-link {{ request()->routeIs('bahan-non-forming.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Bahan Non Forming</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item has-treeview {{ request()->routeIs('jenis-emulsi.*') || request()->routeIs('total-pemakaian-emulsi.*') || request()->routeIs('nomor-emulsi.*') || request()->routeIs('bahan-emulsi.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('jenis-emulsi.*') || request()->routeIs('total-pemakaian-emulsi.*') || request()->routeIs('nomor-emulsi.*') || request()->routeIs('bahan-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-flask nav-icon"></i>
                      <p>
                          Emulsi
                        <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ route ('jenis-emulsi.index')}}" class="nav-link {{ request()->routeIs('jenis-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Jenis Emulsi</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('total-pemakaian-emulsi.index')}}" class="nav-link {{ request()->routeIs('total-pemakaian-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Total Pemakaian Emulsi</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('nomor-emulsi.index')}}" class="nav-link {{ request()->routeIs('nomor-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Nomor Proses Emulsi</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('bahan-emulsi.index')}}" class="nav-link {{ request()->routeIs('bahan-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Bahan Emulsi</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('suhu-adonan.index')}}" class="nav-link {{ request()->is('*suhu-adonan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Suhu Adonan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('jenis-better.index')}}" class="nav-link {{ request()->is('*jenis-better*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Formula Better</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('jenis-breader.index')}}" class="nav-link {{ request()->is('*jenis-breader*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Jenis Breader</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('jenis-predust.index')}}" class="nav-link {{ request()->is('*jenis-predust*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Jenis Predust</p>
                    </a>
                  </li>
                  <li class="nav-item has-treeview {{ request()->routeIs('suhu-frayer-1.*') || request()->routeIs('suhu-frayer-2.*') || request()->routeIs('waktu-penggorengan.*') || request()->routeIs('waktu-penggorengan-2.*') || request()->routeIs('std-suhu-pusat.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('suhu-frayer-1.*') || request()->routeIs('suhu-frayer-2.*') || request()->routeIs('waktu-penggorengan.*') || request()->routeIs('waktu-penggorengan-2.*') || request()->routeIs('std-suhu-pusat.*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-fire nav-icon"></i>
                      <p>
                        Penggorengan
                        <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ route ('suhu-frayer-1.index')}}" class="nav-link {{ request()->routeIs('suhu-frayer-1.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Suhu Frayer</p>
                        </a>
                      </li>
                      <!-- <li class="nav-item">
                        <a href="{{ route('suhu-frayer-2.index') }}" class="nav-link {{ request()->routeIs('suhu-frayer-2.*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Suhu Frayer (2)</p>
                        </a>
                      </li> -->
                      <li class="nav-item">
                        <a href="{{ route ('std-suhu-pusat.index')}}" class="nav-link {{ request()->is('*std-suhu-pusat*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> STD Suhu Pusat</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item has-treeview {{ request()->is('*suhu-blok*') || request()->is('*std-fan*') || request()->is('*std-suhu-pusat-roasting*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('*suhu-blok*') || request()->is('*std-fan*') || request()->is('*std-suhu-pusat-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-snowflake nav-icon"></i>
                      <p>
                        Roasting
                        <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ route ('suhu-blok.index')}}" class="nav-link {{ request()->is('*suhu-blok*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Suhu Pemasakan</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('std-fan.index')}}" class="nav-link {{ request()->is('*std-fan*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Standart Fan/Humidity</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('std-suhu-pusat-roasting.index')}}" class="nav-link {{ request()->is('*std-suhu-pusat-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> STD Suhu Pusat</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item has-treeview {{ request()->is('*nama-formula-fla*') || request()->is('*nomor-step-formula-fla*') || request()->is('*bahan-formula-fla*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('*nama-formula-fla*') || request()->is('*nomor-step-formula-fla*') || request()->is('*bahan-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class=" fas fa-th nav-icon"></i>
                      <p>
                        Produk Fla
                        <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ route ('nama-formula-fla.index')}}" class="nav-link {{ request()->is('*nama-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Formula FLA</p>
                        </a>
                      </li>
                      <!-- <li class="nav-item">
                        <a href="{{ route ('nomor-step-formula-fla.index')}}" class="nav-link {{ request()->is('*nomor-step-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Step Formula</p>
                        </a>  
                      </li>            -->
                      <li class="nav-item">
                        <a href="{{ route ('bahan-formula-fla.index')}}" class="nav-link {{ request()->is('*bahan-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Bahan Fla</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route ('std-berat-rheon.index')}}" class="nav-link {{ request()->is('*std-berat-rheon*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Input Std Rheon</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item has-treeview {{ request()->is('*bahan-baku-rice-bites*')  ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('*bahan-baku-rice-bites*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-seedling nav-icon"></i>
                      <p>
                        Rice Bites
                        <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ route ('bahan-baku-rice-bites.index')}}" class="nav-link {{ request()->is('*bahan-baku-rice-bites*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p> Bahan Baku Rice Bites</p>
                        </a>
                      </li>
                      
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('std-salinitas-viskositas.index')}}" class="nav-link {{ request()->is('*std-salinitas-viskositas*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Salinitas dan Viskositas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-tumbling.index')}}" class="nav-link {{ request()->is('*data-tumbling*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data Tumbling</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('input-mesin-peralatan.index')}}" class="nav-link {{ request()->is('*input-mesin-peralatan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Input Verifikasi Kebersihan Ruangan Mesin & Peralatan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('list-peralatan.index')}}" class="nav-link {{ request()->is('*list-peralatan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Master List Peralatan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-bag.index')}}" class="nav-link {{ request()->is('*data-bag*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Standard BAG/PACK</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-box.index')}}" class="nav-link {{ request()->is('*data-box*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Standart BOX</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-timbangan.index')}}" class="nav-link {{ request()->is('*data-timbangan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data Timbangan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('data-thermo.index')}}" class="nav-link {{ request()->is('*data-thermo*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Data Thermometer</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route ('master-cip.index')}}" class="nav-link {{ request()->is('*master-cip*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Master Cip</p>
                    </a>
                  </li>
                </ul>
              </li>
            @endif
              <!-- MENU QC SISTEM -->
              @php
                $receivingAccess = app(\App\Services\ReceivingInspection\ReceivingInspectionAccess::class);
                $isQcUser = $receivingAccess->isQc(auth()->user());
                $isAdminUser = $receivingAccess->isAdmin(auth()->user());
              @endphp
              <li class="nav-item has-treeview {{ request()->is('qc-sistem/chillroom*') || request()->is('qc-sistem/seasoning*') || request()->is('qc-sistem/shoestring*') || request()->is('qc-sistem/rebox*') || request()->is('qc-sistem/pemeriksaan-bahan-kemas*') || request()->is('receiving-inspections*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->is('qc-sistem/chillroom*') || request()->is('qc-sistem/seasoning*') 
                || request()->is('qc-sistem/shoestring*') || request()->is('qc-sistem/rebox*') || request()->is('qc-sistem/pemeriksaan-bahan-kemas*') || request()->is('receiving-inspections*') ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-inbox"></i>
                  <p>
                    Pemeriksaan Kedatangan
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @if($isQcUser || $isAdminUser)
                  <li class="nav-item">
                    <a href="{{ url('/receiving-inspections') }}" class="nav-link {{ request()->is('receiving-inspections*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> Merged Receiving Inspection</p>
                    </a>
                  </li>
                  @endif
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/chillroom') }}" class="nav-link {{ request()->is('qc-sistem/chillroom*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-01/00] Chillroom</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/seasoning') }}" class="nav-link {{ request()->is('qc-sistem/seasoning*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-01/00] Seasoning</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/shoestring') }}" class="nav-link {{ request()->is('qc-sistem/shoestring*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-02/00] Shoestring</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemeriksaan-bahan-kemas') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-bahan-kemas*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-03/00] Kemasan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/rebox') }}" class="nav-link {{ request()->is('qc-sistem/rebox*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-34/00] Rebox</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item has-treeview {{ request()->is('qc-sistem/persiapan-bahan-forming*') || request()->is('qc-sistem/persiapan-bahan-emulsi*') || request()->is('qc-sistem/persiapan-bahan-better*') || request()->is('qc-sistem/wizard/proses-4*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-forming*') || request()->is('qc-sistem/persiapan-bahan-emulsi*') || request()->is('qc-sistem/persiapan-bahan-better*') || request()->is('qc-sistem/wizard/proses-4*') ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-clipboard-list"></i>
                  <p>
                    Meat Preparation 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/persiapan-bahan-forming') }}" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-forming*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-04/00] Cold Mixing</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/persiapan-bahan-emulsi') }}" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-emulsi*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-05/00] Bahan Emulsi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/persiapan-bahan-better') }}" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-better*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-06/00] Bahan Better</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('wizard.proses4.index') }}" class="nav-link {{ request()->is('qc-sistem/wizard/proses-4*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p> [QF-07/00] Verifikasi Proses Tumbling</p>
                    </a>
                  </li>
                </ul>
              </li>
  
              @if(in_array(auth()->user()->id_role, [1, 2, 3, 4, 5, 6, 7])) <!-- Semua role termasuk Manager (7) -->
              <!-- MENU WIZARD -->
              @php
                $isWizardMenu = (request()->is('qc-sistem/wizard-selection*') 
                  || (request()->is('qc-sistem/wizard/*') && !request()->is('qc-sistem/wizard/proses-4*'))
                  || request()->is('qc-sistem/verifikasi-berat-produk*'));
              @endphp
              <li class="nav-item">
                <a href="{{ url('/qc-sistem/wizard-selection') }}" class="nav-link {{ $isWizardMenu ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-magic"></i>
                  <p>
                    Cooking QC
                  </p>
                </a>
              </li>
              <li class="nav-item has-treeview {{ request()->segment(2) == 'proses-twahing' ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->segment(2) == 'proses-twahing' ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-thermometer-half"></i>
                  <p>
                    Pemeriksaan Thawing
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/proses-twahing') }}" class="nav-link {{ request()->is('qc-sistem/proses-twahing*') ? 'active' : '' }}" style="font-size:13px;">
                      <i class="nav-icon fas fa-thermometer-half"></i>
                      <p>[QF-33/00] Proses Thawing</p>
                    </a>
                  </li>
                </ul>
              </li>
              
              <li class="nav-item has-treeview {{ in_array(request()->segment(2), ['pengemasan-produk','pengemasan-plastik','berat-produk','pengemasan-karton','dokumentasi','input-metal-detector','pembuatan-sample','wizards','label-pemeriksaan','release-produk']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array(request()->segment(2), ['pengemasan-produk','pengemasan-plastik','berat-produk','pengemasan-karton','dokumentasi','input-metal-detector','pembuatan-sample','wizards','label-pemeriksaan','release-produk']) ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-box"></i>
                  <p>
                    Packing
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                   <li class="nav-item">
                    <a href="{{ url('/qc-sistem/wizards/packing') }}" class="nav-link {{ request()->is('qc-sistem/wizards/packing*') ? 'active' : '' }}" style="font-size:13px;">
                      <i class="nav-icon fas fa-boxes"></i>
                      <p>[QF-15/00] Verifikasi Pengemasan</p>
                    </a>
                  </li>
                  <!-- <li class="nav-item">
                    <a href="{{ route('release-produk.index') }}" class="nav-link {{ request()->is('qc-sistem/release-produk*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>Release Produk</p>
                    </a>
                  </li> -->
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pembuatan-sample') }}" class="nav-link {{ request()->is('qc-sistem/pembuatan-sample*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-17/00] Pembuatan Sample Produk</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/input-metal-detector') }}" class="nav-link {{ request()->is('qc-sistem/input-metal-detector*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-18/00] Metal Detector</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('label-pemeriksaan.index') }}" class="nav-link {{ request()->is('qc-sistem/label-pemeriksaan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-36/00] Label Pemeriksaan</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item has-treeview {{ request()->segment(2) == 'produk-yum' ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->segment(2) == 'produk-yum' ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-calendar-check"></i>
                  <p>
                     KPI Berat Produk YUM
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/produk-yum') }}" class="nav-link {{ request()->is('qc-sistem/produk-yum*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-16/00] KPI Produk YUM</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item has-treeview {{ request()->segment(2) == 'pemeriksaan-produk-cooking-mixer-fla' || request()->segment(2) == 'pemeriksaan-rheon-machine' ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->segment(2) == 'pemeriksaan-produk-cooking-mixer-fla' || request()->segment(2) == 'pemeriksaan-rheon-machine' ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-th"></i>
                  <p>
                    Produk Fla
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemeriksaan-produk-cooking-mixer-fla') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-produk-cooking-mixer-fla*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-12/00] Cooking Mixer</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemeriksaan-rheon-machine') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-rheon-machine*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-19/00] Rheon Machine</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item has-treeview {{ request()->segment(2) == 'pemeriksaan-rice-bites' || request()->segment(2) == 'pemasakan-nasi' ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->segment(2) == 'pemeriksaan-rice-bites' || request()->segment(2) == 'pemasakan-nasi' ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-indent"></i>
                  <p>
                    Produk Rice Bites
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemasakan-nasi') }}" class="nav-link {{ request()->is('qc-sistem/pemasakan-nasi*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-13/00] Pemasakan Nasi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemeriksaan-rice-bites') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-rice-bites*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-14/00] Pemeriksaan Produk Rice Bites</p>
                    </a>
                  </li>
                </ul>
              </li>
              @php
                $nonProduksiSegments = ['produk-forming','produk-non-forming','ketidaksesuaian-plastik','pemeriksaan-benda-asing','pemeriksaan-proses-produksi','area-proses','gmp-karyawan','kontrol-sanitasi','barang-mudah-pecah','verif-peralatan','verif-cip','timbangan','thermometer','inventaris-qc'];
              @endphp
              <li class="nav-item has-treeview {{ in_array(request()->segment(2), $nonProduksiSegments) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array(request()->segment(2), $nonProduksiSegments) ? 'active' : '' }}" style="font-size:13px;">
                  <i class="nav-icon fas fa-clipboard-list"></i>
                  <p>
                    Non Proses Produksi
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item has-treeview {{ in_array(request()->segment(2), ['produk-forming', 'produk-non-forming']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ in_array(request()->segment(2), ['produk-forming', 'produk-non-forming']) ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-redo-alt nav-icon" style="font-size:10px;"></i>
                      <p>
                        Pergantian Proses Produksi
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ url('/qc-sistem/produk-forming') }}" class="nav-link {{ request()->is('qc-sistem/produk-forming*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p>[QF-20/00] Produk Forming</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ url('/qc-sistem/produk-non-forming') }}" class="nav-link {{ request()->is('qc-sistem/produk-non-forming*') ? 'active' : '' }}" style="font-size:12px;">
                          <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                          <p>[QF-21/00] Produk Non Forming</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/ketidaksesuaian-plastik') }}" class="nav-link {{ request()->is('qc-sistem/ketidaksesuaian-plastik*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-22/00] Ketidaksesuaian Plastik</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemeriksaan-benda-asing') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-benda-asing*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-23/00] Kontaminasi Benda Asing</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/pemeriksaan-proses-produksi') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-proses-produksi*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-24/00] Ketidaksesuaian Proses Produksi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/area-proses') }}" class="nav-link {{ request()->is('qc-sistem/area-proses*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-25/00] Pemeriksaan Kondisi Area Kerja Selama Produksi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/verif-peralatan') }}" class="nav-link {{ request()->is('qc-sistem/verif-peralatan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-26/00] Verifikasi Kebersihan Ruangan Mesin & Peralatan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/verif-cip') }}" class="nav-link {{ request()->is('qc-sistem/verif-cip*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-27/00] Pemeriksaan CIP</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/gmp-karyawan') }}" class="nav-link {{ request()->is('qc-sistem/gmp-karyawan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-28/00] GMP Karyawan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/kontrol-sanitasi') }}" class="nav-link {{ request()->is('qc-sistem/kontrol-sanitasi*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-29/00] Kontrol Sanitasi</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/barang-mudah-pecah') }}" class="nav-link {{ request()->is('qc-sistem/barang-mudah-pecah*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-30/00] Barang Mudah Pecah</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/timbangan') }}" class="nav-link {{ request()->is('qc-sistem/timbangan*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-31/00] Timbangan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('/qc-sistem/thermometer') }}" class="nav-link {{ request()->is('qc-sistem/thermometer*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-32/00] Thermometer</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('inventaris-qc.index') }}" class="nav-link {{ request()->is('inventaris-qc*') ? 'active' : '' }}" style="font-size:12px;">
                      <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                      <p>[QF-39/00] Checklist Inventaris Peralatan QC</p>
                    </a>
                  </li>
                </ul>
              </li>
            @endif
            @if(auth()->user()->id_role == 5)
              <li class="nav-header">PENGATURAN</li>
              <li class="nav-item">
                <a href="{{ url('/super-admin/profile') }}" class="nav-link" style="font-size:13px;">
                  <i class="nav-icon fa fa-user-alt"></i>
                  <p>
                    Profile
                    <span class="badge badge-info right">2</span>
                  </p>
                </a>
              </li>
            @endif
          </ul>
      </nav>
    </div>
</aside>