@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('data-barang.index') }}">Data Barang</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                                <i class="fas fa-plus text-primary mr-2"></i>
                                Form Tambah Data Barang
                            </h3>
                        </div>
                        <form action="{{ route('data-barang.store') }}" method="POST">
                            @csrf
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
                                                            {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
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
                                
                                <!-- Dynamic Form untuk Nama Barang -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Nama Barang</h5>
                                        <button type="button" class="btn btn-success btn-sm float-right" id="btnTambahBarang">
                                            <i class="fas fa-plus"></i> Tambah Nama Barang
                                        </button>
                                    </div>
                                    <div class="card-body">
                                     <div id="dynamic-barang-wrapper">
                                            <div class="barang-item mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Nama Barang</label>
                                                        <input type="text" name="nama_barang[]" class="form-control @error('nama_barang.0') is-invalid @enderror" 
                                                               value="{{ old('nama_barang.0') }}" placeholder="Masukkan Nama Barang" required>
                                                        @error('nama_barang.0')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>Jumlah (opsional)</label>
                                                        <input type="number" name="jumlah[]" class="form-control @error('jumlah.0') is-invalid @enderror" 
                                                               value="{{ old('jumlah.0', 0) }}" placeholder="Masukkan Jumlah" min="0">
                                                        @error('jumlah.0')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block remove-barang" disabled>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="">
                                    <a href="{{ route('data-barang.index') }}" class="btn btn-md btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fas fa-save mr-2"></i>Simpan Data
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
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('dynamic-barang-wrapper');
    const addButton = document.getElementById('btnTambahBarang');
    let index = 1;

    const planSelect = document.getElementById('id_plan');
    const areaSelect = document.getElementById('id_area');

    function setAreaLoading() {
        if (!areaSelect) return;
        areaSelect.innerHTML = '<option value="">Memuat...</option>';
    }

    function setAreaPlaceholder() {
        if (!areaSelect) return;
        areaSelect.innerHTML = '<option value="">Pilih Area</option>';
    }

    function loadAreasByPlan(planId, selectedAreaId) {
        if (!areaSelect) return;
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

    if (planSelect && areaSelect) {
        planSelect.addEventListener('change', function() {
            loadAreasByPlan(this.value, null);
        });

        const oldPlan = '{{ old('id_plan') }}';
        const oldArea = '{{ old('id_area') }}';
        if (oldPlan) {
            loadAreasByPlan(oldPlan, oldArea);
        }
    }

    // Fungsi untuk menambahkan form barang baru
    function addBarangItem() {
        const barangHTML = `
        <div class="barang-item mb-3" data-index="${index}">
            <div class="row">
                <div class="col-md-6">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang[]" class="form-control" placeholder="Masukkan Nama Barang" required>
                </div>
                <div class="col-md-4">
                    <label>Jumlah (opsional)</label>
                    <input type="number" name="jumlah[]" class="form-control" value="0" placeholder="Masukkan Jumlah" min="0">
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
        
        container.insertAdjacentHTML('beforeend', barangHTML);
        
        // Aktifkan tombol hapus untuk item pertama jika baru ada 2 item
        if (container.children.length === 2) {
            const firstRemoveBtn = container.firstElementChild.querySelector('.remove-barang');
            if (firstRemoveBtn) {
                firstRemoveBtn.disabled = false;
            }
        }
        
        index++;
    }

    // Event listener untuk tombol tambah - pastikan hanya satu kali
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            addBarangItem();
        });
    }

    // Event delegation untuk tombol hapus
    if (container) {
        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-barang');
            if (removeBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const item = removeBtn.closest('.barang-item');
                if (item) {
                    item.remove();
                    
                    // Nonaktifkan tombol hapus jika hanya tersisa 1 item
                    if (container.children.length === 1) {
                        const firstRemoveBtn = container.firstElementChild.querySelector('.remove-barang');
                        if (firstRemoveBtn) {
                            firstRemoveBtn.disabled = true;
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
                                