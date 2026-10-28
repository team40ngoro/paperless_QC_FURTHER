@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class=""><i class="fas fa-tools text-primary mr-2"></i>Tambah List Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list-peralatan.index') }}">List Peralatan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                Form Tambah Master Data List Peralatan
                            </h3>
                        </div>

                        <form action="{{ route('list-peralatan.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <h5 class="alert-heading "><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan input:</h5>
                                        <ul class="mb-0 pl-3">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(auth()->user()->role === 'superadmin')
                                <div class="form-group mb-3">
                                    <label for="id_plan_select" class="">Plan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="id_plan_select" name="id_plan">
                                        <option value="">-- Pilih Plan --</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Memilih plan akan menyaring daftar area di bawah ini.</small>
                                </div>
                                @endif

                                <div class="form-group mb-4">
                                    <label for="id_area" class="">Area <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('id_area') is-invalid @enderror" id="id_area" name="id_area" required>
                                        <option value="">-- Pilih Area --</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}" data-plan-id="{{ $area->id_plan }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                                {{ $area->area }} @if($area->plan) ({{ $area->plan->nama_plan }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="card card-outline card-info mb-3">
                                    <div class="card-header d-flex align-items-center bg-light py-2">
                                        <h5 class="card-title text-secondary mb-0">
                                            <i class="fas fa-list-ol mr-2"></i>Daftar Alat / Peralatan & Jumlah
                                        </h5>
                                        <button type="button" id="add-alat-btn" class="btn btn-success btn-sm ml-auto">
                                            <i class="fas fa-plus mr-1"></i> Tambah Baris Alat
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="alat-container">
                                            @php
                                                $oldAlat = old('list_alat', ['']);
                                                $oldJumlah = old('jumlah_alat', [1]);
                                            @endphp
                                            @foreach($oldAlat as $index => $val)
                                                <div class="input-group mb-3 alat-row">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light row-number">#{{ $index + 1 }}</span>
                                                    </div>
                                                    <input type="text" name="list_alat[]" class="form-control text-uppercase @error('list_alat.'.$index) is-invalid @enderror" 
                                                           placeholder="Masukkan nama peralatan / alat..." 
                                                           value="{{ $val }}" required>
                                                    <div class="input-group-prepend input-group-append">
                                                        <span class="input-group-text bg-light font-weight-bold">Jumlah:</span>
                                                    </div>
                                                    <input type="number" name="jumlah_alat[]" class="form-control" style="max-width: 120px;" 
                                                           value="{{ $oldJumlah[$index] ?? 1 }}" min="1" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-danger remove-alat-btn" type="button" title="Hapus Baris">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-between py-3">
                                <a href="{{ route('list-peralatan.index') }}" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save mr-2"></i>Simpan Data
                                </button>
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
    const container = document.getElementById('alat-container');
    const addBtn = document.getElementById('add-alat-btn');
    const planSelect = document.getElementById('id_plan_select');
    const areaSelect = document.getElementById('id_area');

    // Filter area by plan if plan is changed
    if (planSelect && areaSelect) {
        planSelect.addEventListener('change', function () {
            const selectedPlanId = this.value;
            const areaOptions = areaSelect.querySelectorAll('option');

            areaOptions.forEach(opt => {
                if (!opt.value) return; // skip placeholder
                const planId = opt.getAttribute('data-plan-id');
                if (!selectedPlanId || planId === selectedPlanId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            // Reset selected area if it's no longer visible
            const currentSelected = areaSelect.options[areaSelect.selectedIndex];
            if (currentSelected && currentSelected.style.display === 'none') {
                areaSelect.value = '';
                if ($(areaSelect).hasClass('select2-hidden-accessible')) {
                    $(areaSelect).val('').trigger('change');
                }
            }
        });
    }

    // Dynamic row index update
    const updateRowNumbers = () => {
        container.querySelectorAll('.alat-row').forEach((row, i) => {
            const numSpan = row.querySelector('.row-number');
            if (numSpan) numSpan.textContent = `#${i + 1}`;
        });
    };

    // Add new equipment row
    addBtn.addEventListener('click', function () {
        const rowCount = container.querySelectorAll('.alat-row').length;
        const newRow = document.createElement('div');
        newRow.classList.add('input-group', 'mb-3', 'alat-row');
        newRow.innerHTML = `
            <div class="input-group-prepend">
                <span class="input-group-text bg-light row-number">#${rowCount + 1}</span>
            </div>
            <input type="text" name="list_alat[]" class="form-control text-uppercase" 
                   placeholder="Masukkan nama peralatan / alat..." required>
            <div class="input-group-prepend input-group-append">
                <span class="input-group-text bg-light font-weight-bold">Jumlah:</span>
            </div>
            <input type="number" name="jumlah_alat[]" class="form-control" style="max-width: 120px;" 
                   value="1" min="1" required>
            <div class="input-group-append">
                <button class="btn btn-danger remove-alat-btn" type="button" title="Hapus Baris">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        updateRowNumbers();
    });

    // Remove row event delegation
    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-alat-btn')) {
            const rows = container.querySelectorAll('.alat-row');
            if (rows.length > 1) {
                e.target.closest('.remove-alat-btn').closest('.alat-row').remove();
                updateRowNumbers();
            } else {
                const input = rows[0].querySelector('input[name="list_alat[]"]');
                if (input) input.value = '';
            }
        }
    });
});
</script>
@endpush
