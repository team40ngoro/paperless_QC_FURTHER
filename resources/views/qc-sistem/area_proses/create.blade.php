@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Pemeriksaan Kondisi Area Kerja Selama Produksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('area-proses.index') }}">Pemeriksaan Kondisi Area Kerja Selama Produksi</a></li>
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
                                <i class="fas fa-plus text-success mr-2"></i>
                                Form Tambah Pemeriksaan Kondisi Area Kerja Selama Produksi
                            </h3>
                        </div>
                        <form action="{{ route('area-proses.store') }}" method="POST">
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
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal" id="tanggal"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- End shift row -->
                                </div>
                                
                                <div id="area-proses-container">
                                    <div class="area-proses-row card card-outline card-secondary mb-4 p-3 shadow-sm" data-row="0">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="text-primary mb-0"><i class="fas fa-industry"></i> Data Area Proses #<span class="row-number">1</span></h5>
                                            <button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Area <span class="text-danger">*</span></label>
                                                    <select class="form-control id_area" name="area_id[]" required>
                                                        <option value="">Pilih Area</option>
                                                        @foreach($areas as $area)
                                                            <option value="{{ $area->id }}">{{ $area->area }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" name="jam[]" class="form-control" value="{{ now()->format('H:i') }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Kebersihan Ruangan <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="kebersihan_ruangan[]" required>
                                                        <option value="OK" selected>✓ OK</option>
                                                        <option value="Kotor">✗ Kotor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 kondisi_barang_container" style="display: none;">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Kondisi Barang <span class="text-danger">*</span></label>
                                                    <select class="form-control kondisi-barang-select" name="kondisi_barang[]">
                                                        <option value="OK" selected>✓ OK</option>
                                                        <option value="NOT OK">✗ Tidak OK</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Kebersihan Karyawan <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="kebersihan_karyawan[]" required>
                                                        <option value="OK" selected>✓ OK</option>
                                                        <option value="Kotor">✗ Kotor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Pem. Suhu Ruang (°C) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="pemeriksaan_suhu_ruang[]" placeholder="Suhu (°C)" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3 suhu_rm_container" style="display: none;">
                                                <div class="form-group mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="font-weight-bold mb-0">Suhu RM <span class="text-danger">*</span></label>
                                                        <button type="button" class="btn btn-xs btn-outline-success btn-add-suhu-rm" title="Tambah Suhu RM">
                                                            <i class="fas fa-plus"></i> Tambah Suhu
                                                        </button>
                                                    </div>
                                                    <div class="suhu-rm-wrapper">
                                                        <div class="input-group mb-2 suhu-rm-item">
                                                            <input type="text" class="form-control suhu-rm-input" name="suhu_rm[0][]" placeholder="Suhu RM">
                                                            <div class="input-group-append remove-suhu-rm-wrapper" style="display:none;">
                                                                <button type="button" class="btn btn-outline-danger btn-remove-suhu-rm" title="Hapus Suhu RM">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Ketidaksesuaian</label>
                                                    <textarea class="form-control" name="ketidaksesuaian[]" rows="2" placeholder="Ketidaksesuaian jika ada"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="font-weight-bold">Tindakan Koreksi</label>
                                                    <textarea class="form-control" name="tindakan_koreksi[]" rows="2" placeholder="Tindakan koreksi"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <button type="button" class="btn btn-outline-success btn-block shadow-sm" id="add-row">
                                        <i class="fas fa-plus-circle"></i> Tambah Baris Area Proses
                                    </button>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="">
                                    <a href="{{ route('area-proses.index') }}" class="btn btn-md btn-secondary">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('area-proses-container');
        const addButton = document.getElementById('add-row');

        // Inisialisasi Select2 untuk baris pertama
        $('.id_area').select2({
            placeholder: "Pilih Area",
            allowClear: true
        });

        function updateSuhuRmItemRemoveButtons(suhuRmWrapper) {
            const items = suhuRmWrapper.querySelectorAll('.suhu-rm-item');
            items.forEach(item => {
                const removeBtnWrapper = item.querySelector('.remove-suhu-rm-wrapper');
                if (removeBtnWrapper) {
                    removeBtnWrapper.style.display = items.length > 1 ? 'block' : 'none';
                }
            });
        }

        function updateRowUI() {
            const rows = container.querySelectorAll('.area-proses-row');
            rows.forEach((row, index) => {
                row.setAttribute('data-row', index);
                row.querySelector('.row-number').textContent = index + 1;
                const removeBtn = row.querySelector('.remove-row');
                removeBtn.style.display = rows.length > 1 ? 'block' : 'none';

                // Update name atribut suhu_rm untuk baris ke-index
                const suhuRmInputs = row.querySelectorAll('.suhu-rm-input');
                suhuRmInputs.forEach(input => {
                    input.name = `suhu_rm[${index}][]`;
                });

                const suhuRmWrapper = row.querySelector('.suhu-rm-wrapper');
                if (suhuRmWrapper) {
                    updateSuhuRmItemRemoveButtons(suhuRmWrapper);
                }
            });
        }

        // Handle conditional display for kondisi_barang and suhu_rm
        function toggleDynamicFields(selectElement) {
            const row = selectElement.closest('.area-proses-row');
            if (!row) return;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const selectedText = selectedOption ? selectedOption.text.trim().toLowerCase() : '';
            const cleanText = selectedText.replace(/[\s\-_]/g, '');
            
            const kondisiContainer = row.querySelector('.kondisi_barang_container');
            const kondisiSelect = row.querySelector('.kondisi-barang-select');

            if (kondisiContainer) {
                if (cleanText.includes('chillroom') || cleanText.includes('seasoning')) {
                    kondisiContainer.style.display = 'block';
                    if (kondisiSelect) kondisiSelect.setAttribute('required', 'required');
                } else {
                    kondisiContainer.style.display = 'none';
                    if (kondisiSelect) {
                        kondisiSelect.removeAttribute('required');
                        kondisiSelect.value = '';
                    }
                }
            }

            const suhuRMContainer = row.querySelector('.suhu_rm_container');
            const firstSuhuRMInput = row.querySelector('.suhu-rm-input');

            if (suhuRMContainer) {
                if (cleanText.includes('chillroom')) {
                    suhuRMContainer.style.display = 'block';
                    if (firstSuhuRMInput) firstSuhuRMInput.setAttribute('required', 'required');
                } else {
                    suhuRMContainer.style.display = 'none';
                    row.querySelectorAll('.suhu-rm-input').forEach(input => {
                        input.removeAttribute('required');
                        input.value = '';
                    });
                    // Sisa kan 1 item suhu_rm saja saat disembunyikan
                    const suhuRmWrapper = row.querySelector('.suhu-rm-wrapper');
                    if (suhuRmWrapper) {
                        const items = suhuRmWrapper.querySelectorAll('.suhu-rm-item');
                        for (let i = 1; i < items.length; i++) {
                            items[i].remove();
                        }
                        updateSuhuRmItemRemoveButtons(suhuRmWrapper);
                    }
                }
            }
        }

        // Gunakan jQuery delegation agar menangkap event dari Select2
        $(container).on('change select2:select select2:clear', '.id_area', function() {
            toggleDynamicFields(this);
        });

        // Event listener untuk Tambah Suhu RM
        $(container).on('click', '.btn-add-suhu-rm', function() {
            const row = this.closest('.area-proses-row');
            const rowIndex = row.getAttribute('data-row') || 0;
            const suhuRmWrapper = row.querySelector('.suhu-rm-wrapper');

            const newItem = document.createElement('div');
            newItem.className = 'input-group mb-2 suhu-rm-item';
            newItem.innerHTML = `
                <input type="text" class="form-control suhu-rm-input" name="suhu_rm[${rowIndex}][]" placeholder="Suhu RM">
                <div class="input-group-append remove-suhu-rm-wrapper">
                    <button type="button" class="btn btn-outline-danger btn-remove-suhu-rm" title="Hapus Suhu RM">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            suhuRmWrapper.appendChild(newItem);
            updateSuhuRmItemRemoveButtons(suhuRmWrapper);
        });

        // Event listener untuk Hapus item Suhu RM
        $(container).on('click', '.btn-remove-suhu-rm', function() {
            const suhuRmItem = this.closest('.suhu-rm-item');
            const suhuRmWrapper = this.closest('.suhu-rm-wrapper');
            if (suhuRmWrapper.querySelectorAll('.suhu-rm-item').length > 1) {
                suhuRmItem.remove();
                updateSuhuRmItemRemoveButtons(suhuRmWrapper);
            }
        });

        addButton.addEventListener('click', function() {
            const firstRow = container.querySelector('.area-proses-row');
            
            // Destroy Select2 sementara agar struktur yang diclone bersih
            if ($(firstRow).find('.id_area').hasClass('select2-hidden-accessible')) {
                $(firstRow).find('.id_area').select2('destroy');
            }
            
            const newRow = firstRow.cloneNode(true);
            
            // Inisialisasi ulang Select2 pada baris pertama setelah cloning
            $(firstRow).find('.id_area').select2({
                placeholder: "Pilih Area",
                allowClear: true
            });

            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                if (input.name === 'kebersihan_ruangan[]' || input.name === 'kebersihan_karyawan[]') {
                    input.value = 'OK';
                } else if (input.name === 'kondisi_barang[]') {
                    input.value = 'OK';
                } else {
                    input.value = '';
                }
            });

            const newKondisiContainer = newRow.querySelector('.kondisi_barang_container');
            if (newKondisiContainer) {
                newKondisiContainer.style.display = 'none';
            }
            const newKondisiSelect = newRow.querySelector('.kondisi-barang-select');
            if (newKondisiSelect) {
                newKondisiSelect.removeAttribute('required');
            }

            const newSuhuRmContainer = newRow.querySelector('.suhu_rm_container');
            if (newSuhuRmContainer) {
                newSuhuRmContainer.style.display = 'none';
            }
            
            // Reset wrapper suhu rm di baris baru agar hanya memiliki 1 input
            const newSuhuRmWrapper = newRow.querySelector('.suhu-rm-wrapper');
            if (newSuhuRmWrapper) {
                const items = newSuhuRmWrapper.querySelectorAll('.suhu-rm-item');
                for (let i = 1; i < items.length; i++) {
                    items[i].remove();
                }
                const firstInput = newSuhuRmWrapper.querySelector('.suhu-rm-input');
                if (firstInput) {
                    firstInput.value = '';
                    firstInput.removeAttribute('required');
                }
            }

            container.appendChild(newRow);
            
            // Inisialisasi Select2 pada baris baru
            $(newRow).find('.id_area').select2({
                placeholder: "Pilih Area",
                allowClear: true
            });
            
            updateRowUI();
            const newSelect = newRow.querySelector('.id_area');
            if (newSelect) toggleDynamicFields(newSelect);
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
                const rows = container.querySelectorAll('.area-proses-row');
                if (rows.length > 1) {
                    const row = e.target.closest('.area-proses-row');
                    row.remove();
                    updateRowUI();
                }
            }
        });

        updateRowUI();
        container.querySelectorAll('.id_area').forEach(select => {
            toggleDynamicFields(select);
        });
    });
</script>
@endpush
@endsection
