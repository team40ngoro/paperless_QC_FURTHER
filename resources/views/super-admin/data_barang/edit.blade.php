@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('data-barang.index') }}">Data Barang</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit text-warning mr-2"></i>
                                Form Edit Data Barang
                            </h3>
                        </div>
                        <form action="{{ route('data-barang.update', $dataBarang->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_plan') is-invalid @enderror" 
                                                    id="id_plan" name="id_plan" required>
                                                <option value="">Pilih Plan</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" 
                                                            {{ (old('id_plan', $dataBarang->id_plan) == $plan->id) ? 'selected' : '' }}>
                                                        {{ $plan->nama_plan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_area">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area') is-invalid @enderror"
                                                    id="id_area" name="id_area" required>
                                                <option value="">Pilih Area</option>
                                            </select>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('nama_barang') is-invalid @enderror" 
                                                   id="nama_barang" 
                                                   name="nama_barang" 
                                                   value="{{ old('nama_barang', $dataBarang->nama_barang) }}" 
                                                   placeholder="Masukkan nama barang"
                                                   required>
                                            @error('nama_barang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   class="form-control @error('jumlah') is-invalid @enderror" 
                                                   id="jumlah" 
                                                   name="jumlah" 
                                                   value="{{ old('jumlah', $dataBarang->jumlah) }}" 
                                                   placeholder="Masukkan jumlah"
                                                   min="0"
                                                   required>
                                            @error('jumlah')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Info User yang membuat -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Informasi:</strong> Data ini dibuat oleh 
                                            <span class="badge badge-primary">{{ $dataBarang->user->name ?? 'Unknown' }}</span>
                                            pada tanggal {{ $dataBarang->created_at ? $dataBarang->created_at->format('d-m-Y H:i:s') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="">
                                    <a href="{{ route('data-barang.index') }}" class="btn btn-md btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-md btn-warning">
                                        <i class="fas fa-save mr-2"></i>Update Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const planSelect = document.getElementById('id_plan');
        const areaSelect = document.getElementById('id_area');
        if (!planSelect || !areaSelect) return;

        function setAreaLoading() {
            areaSelect.innerHTML = '<option value="">Memuat...</option>';
        }

        function setAreaPlaceholder() {
            areaSelect.innerHTML = '<option value="">Pilih Area</option>';
        }

        function loadAreasByPlan(planId, selectedAreaId) {
            if (!planId) {
                setAreaPlaceholder();
                return;
            }

            setAreaLoading();
            fetch(`{{ url('super-admin/ajax/area-by-plan') }}/${planId}`)
                .then(res => res.json())
                .then(items => {
                    let opts = '<option value="">Pilih Area</option>';
                    if (Array.isArray(items)) {
                        items.forEach(it => {
                            const sel = (String(selectedAreaId) === String(it.id)) ? 'selected' : '';
                            opts += `<option value="${it.id}" ${sel}>${it.area}</option>`;
                        });
                    }
                    areaSelect.innerHTML = opts;
                })
                .catch(() => {
                    areaSelect.innerHTML = '<option value="">Gagal memuat area</option>';
                });
        }

        planSelect.addEventListener('change', function () {
            loadAreasByPlan(this.value, null);
        });

        const currentPlan = '{{ old('id_plan', $dataBarang->id_plan) }}';
        const currentArea = '{{ old('id_area', $dataBarang->id_area) }}';
        loadAreasByPlan(currentPlan, currentArea);
    });
</script>
@endpush