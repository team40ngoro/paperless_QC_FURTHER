@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-exchange-alt mr-2 text-primary"></i> Assign Plan ke Manager</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('grafana.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.profile') }}">Kelola User</a></li>
                <li class="breadcrumb-item active">Assign Plan</li>
                </ol>
            </div>
            </div>
        </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Alert --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card card-primary card-outline elevation-2">
                    <div class="card-header">
                        <h3 class="card-title">
                        <i class="fas fa-user-cog mr-2"></i>
                        Assign Akses Plan untuk: <strong>{{ $user->name }}</strong>
                        </h3>
                        <div class="card-tools">
                        <span class="badge badge-info">{{ $user->roleModel->role ?? '-' }}</span>
                        </div>
                    </div>

                    <form action="{{ route('users.save-assign-plans', $user->uuid) }}" method="POST">
                        @csrf
                        <div class="card-body">

                        {{-- Info User --}}
                        <div class="alert alert-light border mb-4">
                            <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Nama</small>
                                <strong>{{ $user->name }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Username</small>
                                <strong>{{ $user->username }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Plan Asal</small>
                                <strong>{{ $user->plan->nama_plan ?? '-' }}</strong>
                            </div>
                            </div>
                        </div>

                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Centang plan yang boleh diakses oleh Manager ini saat melakukan <strong>Switch Plan</strong>.
                        </p>

                        {{-- Grid Checkbox Plan --}}
                        <div class="row">
                            @forelse($plans as $plan)
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="card card-body py-2 px-3 border position-relative {{ in_array($plan->id, $assignedPlanIds) ? 'border-primary bg-light-blue' : 'border-light' }}"
                                    id="card-plan-{{ $plan->id }}"
                                    style="cursor:pointer; transition: all 0.2s;"
                                    onclick="togglePlan({{ $plan->id }})">
                                @if($user->id_plan == $plan->id)
                                    <span class="badge badge-success position-absolute" style="top: 8px; right: 8px; font-size:9px; padding: 4px 8px;">Plan Asal</span>
                                @endif
                                <div class="d-flex align-items-center">
                                    <input type="checkbox"
                                        name="plan_ids[]"
                                        value="{{ $plan->id }}"
                                        id="plan_{{ $plan->id }}"
                                        class="plan-checkbox mr-3"
                                        style="width:18px; height:18px; cursor:pointer;"
                                        {{ in_array($plan->id, $assignedPlanIds) ? 'checked' : '' }}>
                                    <label for="plan_{{ $plan->id }}" class="mb-0" style="cursor:pointer; font-size:13px;">
                                    <strong>{{ $plan->nama_plan }}</strong>
                                    @if($plan->abbrivation)
                                        <br><small class="text-muted">{{ $plan->abbrivation }}</small>
                                    @endif
                                    </label>
                                </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-warning">Belum ada data Plan. Silakan buat Plan terlebih dahulu.</div>
                            </div>
                            @endforelse
                        </div>

                        {{-- Tombol Pilih Semua / Hapus Semua --}}
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-2" onclick="selectAll()">
                            <i class="fas fa-check-double mr-1"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                            <i class="fas fa-times mr-1"></i> Hapus Semua
                            </button>
                        </div>

                        </div>

                        <div class="card-footer justify-content-between">
                        <a href="{{ route('users.profile') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Akses Plan
                        </button>
                        </div>
                    </form>
                    </div>
                </div>
                </div>

            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePlan(planId) {
    const checkbox = document.getElementById('plan_' + planId);
    const card = document.getElementById('card-plan-' + planId);
    checkbox.checked = !checkbox.checked;
    updateCardStyle(planId, checkbox.checked);
}

// Prevent double-toggle when clicking directly on checkbox/label
document.querySelectorAll('.plan-checkbox').forEach(function(cb) {
    cb.addEventListener('click', function(e) {
        e.stopPropagation();
        updateCardStyle(this.value, this.checked);
    });
});

function updateCardStyle(planId, checked) {
    const card = document.getElementById('card-plan-' + planId);
    if (checked) {
        card.classList.add('border-primary', 'bg-light-blue');
        card.classList.remove('border-light');
    } else {
        card.classList.remove('border-primary', 'bg-light-blue');
        card.classList.add('border-light');
    }
}

function selectAll() {
    document.querySelectorAll('.plan-checkbox').forEach(function(cb) {
        cb.checked = true;
        updateCardStyle(cb.value, true);
    });
}

function deselectAll() {
    document.querySelectorAll('.plan-checkbox').forEach(function(cb) {
        cb.checked = false;
        updateCardStyle(cb.value, false);
    });
}
</script>

<style>
  .bg-light-blue { background-color: #e8f4fd !important; }
</style>
@endpush
