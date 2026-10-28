@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Input Mesin/Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-mesin-peralatan.index') }}">Input Mesin/Peralatan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus text-primary mr-2"></i>
                                Form Tambah Data Input Mesin/Peralatan
                            </h3>
                        </div>

                        <form action="{{ route('input-mesin-peralatan.store') }}" method="POST">
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_area">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area') is-invalid @enderror" id="id_area" name="id_area" required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div id="nama-mesin-container">
                                            @php
                                                $oldNamaMesin = old('nama_mesin');
                                                $oldBagian = old('bagian', []);
                                                $rows = (is_array($oldNamaMesin) && count($oldNamaMesin) > 0) ? $oldNamaMesin : [''];
                                            @endphp

                                            @foreach($rows as $index => $val)
                                                <div class="mesin-row card mb-3">
                                                    <div class="card-header bg-light d-flex align-items-center">
                                                        <span class="font-weight-bold">Jenis Item #{{ $index + 1 }}</span>
                                                        <button class="btn btn-danger btn-sm remove-nama-mesin ml-auto" type="button">
                                                            <i class="fas fa-trash"></i> Hapus Mesin
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="input-group mb-2">
                                                            <input type="text" name="nama_mesin[]" class="form-control" placeholder="Masukkan Item" value="{{ $val }}" required>
                                                        </div>

                                                        <label class="text-muted small">Bagian/Detail Mesin</label>
                                                        <div class="bagian-container" data-mesin-index="{{ $index }}">
                                                            @php
                                                                $bagianRows = (!empty($oldBagian[$index]) && is_array($oldBagian[$index])) ? $oldBagian[$index] : [''];
                                                            @endphp
                                                            @foreach($bagianRows as $bIndex => $bVal)
                                                                <div class="input-group mb-2 bagian-row">
                                                                    <input type="text" name="bagian[{{ $index }}][]" class="form-control" placeholder="Masukkan nama bagian/detail mesin" value="{{ $bVal }}">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-danger remove-bagian" type="button">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-success add-bagian" data-mesin-index="{{ $index }}">
                                                            <i class="fas fa-plus"></i> Tambah Bagian
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button id="add-nama-mesin" type="button" class="btn btn-primary btn-sm mt-1 mb-3">
                                            <i class="fas fa-plus"></i> Tambah Jenis Item Baru
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <span class="font-weight-bold">Jenis Produk</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="jenis_forming" name="jenis_produk[]" value="Forming"
                                                            {{ is_array(old('jenis_produk')) && in_array('Forming', old('jenis_produk')) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="jenis_forming">Forming</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mt-2">
                                                        <input type="checkbox" class="custom-control-input" id="jenis_non_forming" name="jenis_produk[]" value="Non-Forming"
                                                            {{ is_array(old('jenis_produk')) && in_array('Non-Forming', old('jenis_produk')) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="jenis_non_forming">Non-Forming</label>
                                                    </div>
                                                    @error('jenis_produk')
                                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('input-mesin-peralatan.index') }}" class="btn btn-md btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-md btn-primary">
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
    const container = document.getElementById('nama-mesin-container');
    const addMesinBtn = document.getElementById('add-nama-mesin');

    let mesinIndex = container.querySelectorAll('.mesin-row').length - 1;

    const createMesinRow = (prefillNama = '', bagianList = ['']) => {
        mesinIndex++;
        const index = mesinIndex;

        const div = document.createElement('div');
        div.classList.add('mesin-row', 'card', 'mb-3');
        div.innerHTML = `
            <div class="card-header bg-light d-flex align-items-center">
                <span>Jenis Item #${index + 1}</span>
                <button class="btn btn-danger btn-sm remove-nama-mesin ml-auto" type="button">
                    <i class="fas fa-trash"></i> Hapus Mesin
                </button>
            </div>
            <div class="card-body">
                <div class="input-group mb-2">
                    <input type="text" name="nama_mesin[]" class="form-control" placeholder="Masukkan jenis Item" value="${prefillNama}" required>
                </div>

                <label class="text-muted small">Bagian/Detail Mesin</label>
                <div class="bagian-container" data-mesin-index="${index}">
                    ${bagianList.map((bVal, bIndex) => `
                        <div class="input-group mb-2 bagian-row">
                            <input type="text" name="bagian[${index}][]" class="form-control" placeholder="Masukkan nama bagian/detail mesin" value="${bVal}">
                            <div class="input-group-append">
                                <button class="btn btn-danger remove-bagian" type="button">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <button type="button" class="btn btn-sm btn-success add-bagian" data-mesin-index="${index}">
                    <i class="fas fa-plus"></i> Tambah Bagian
                </button>
            </div>
        `;
        container.appendChild(div);
        updateMesinLabels();
    };

    const updateMesinLabels = () => {
        container.querySelectorAll('.mesin-row .card-header span').forEach((span, i) => {
            span.textContent = `Jenis Item #${i + 1}`;
        });
        container.querySelectorAll('.mesin-row').forEach((row, i) => {
            const bagianContainer = row.querySelector('.bagian-container');
            if (bagianContainer) bagianContainer.dataset.mesinIndex = i;
            const addBtn = row.querySelector('.add-bagian');
            if (addBtn) addBtn.dataset.mesinIndex = i;
        });
    };

    addMesinBtn.addEventListener('click', () => createMesinRow());

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-nama-mesin')) {
            const row = e.target.closest('.mesin-row');
            if (container.querySelectorAll('.mesin-row').length > 1) {
                row.remove();
                updateMesinLabels();
            } else {
                const input = row.querySelector('input[name="nama_mesin[]"]');
                if (input) input.value = '';
                const bagianContainer = row.querySelector('.bagian-container');
                if (bagianContainer) bagianContainer.innerHTML = `
                    <div class="input-group mb-2 bagian-row">
                        <input type="text" name="bagian[${0}][]" class="form-control" placeholder="Masukkan nama bagian/detail mesin" value="">
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-bagian" type="button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        if (e.target.closest('.add-bagian')) {
            const btn = e.target.closest('.add-bagian');
            const index = btn.dataset.mesinIndex;
            const bagianContainer = container.querySelector(`.bagian-container[data-mesin-index="${index}"]`);
            if (bagianContainer) {
                const bIndex = bagianContainer.querySelectorAll('.bagian-row').length;
                const div = document.createElement('div');
                div.classList.add('input-group', 'mb-2', 'bagian-row');
                div.innerHTML = `
                    <input type="text" name="bagian[${index}][]" class="form-control" placeholder="Masukkan nama bagian/detail mesin">
                    <div class="input-group-append">
                        <button class="btn btn-danger remove-bagian" type="button">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                bagianContainer.appendChild(div);
            }
        }

        if (e.target.closest('.remove-bagian')) {
            const row = e.target.closest('.bagian-row');
            const container = row.closest('.bagian-container');
            if (container.querySelectorAll('.bagian-row').length > 1) {
                row.remove();
            } else {
                const input = row.querySelector('input');
                if (input) input.value = '';
            }
        }
    });
});
</script>
@endpush