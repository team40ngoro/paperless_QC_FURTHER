@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Edit Pemeriksaan Kondisi Area Kerja Selama Produksi
                        @if($is_audit ?? false)
                            <span class="badge badge-warning ml-2" style="font-size: 0.6em; vertical-align: middle;">MODE AUDIT</span>
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ ($is_audit ?? false) ? route('area-proses.audit') : route('area-proses.index') }}">
                                Data Area Proses{{ ($is_audit ?? false) ? ' (Audit)' : '' }}
                            </a>
                        </li>
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
                                Form Edit Data Area Proses
                                <!-- @if($is_audit ?? false)
                                    <span class="badge badge-purple ml-2" style="background-color: #6f42c1; color: white;"><i class="fas fa-shield-alt"></i> AUDIT MODE</span>
                                @endif -->
                            </h3>
                        </div>
                        <form action="{{ isset($twoHour) && $twoHour ? route('area-proses.twohour.store', $areaProses->uuid) : route('area-proses.update', $areaProses->uuid) }}" method="POST">
                            @csrf
                            @if(!isset($twoHour) || !$twoHour)
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                @if($is_audit ?? false)
                                    <div class="alert alert-warning alert-dismissible fade show border-left border-warning" role="alert" style="border-left-width: 4px !important;">
                                        <i class="fas fa-clipboard-check mr-2"></i>
                                        <strong>Mode Audit:</strong> Anda sedang mengedit <strong>data audit</strong>. Perubahan yang disimpan akan tersimpan ke versi audit, bukan data operasional.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(isset($twoHour) && $twoHour)
                                    <div class="alert alert-info">
                                        <i class="fas fa-clock"></i>
                                        <strong>Mode Edit Per 2 Jam:</strong> Data akan disimpan sebagai record baru dengan waktu yang diperbarui. Data asli tetap tersimpan.
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            {{ (old('shift_id') ?? $areaProses->shift_id) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="area_id" class="font-weight-bold">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('area_id') is-invalid @enderror" 
                                                   id="id_area_select_area_proses" name="area_id" required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" 
                                                            {{ (old('area_id') ?? $areaProses->area_id) == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('area_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="time" 
                                                       class="form-control @error('jam') is-invalid @enderror" 
                                                       id="jam" 
                                                       name="jam" 
                                                       value="{{ old('jam') ?? (isset($twoHour) && $twoHour ? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') : $areaProses->jam) }}" 
                                                       required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="setNowJam()">Sekarang</button>
                                                </div>
                                            </div>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control @error('tanggal') is-invalid @enderror" 
                                                       id="tanggal" 
                                                       name="tanggal" 
                                                       value="{{ old('tanggal') ?? (isset($twoHour) && $twoHour ? \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') : ($areaProses->tanggal ? $areaProses->tanggal->format('Y-m-d') : \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d'))) }}" 
                                                       required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="setNowTanggal()">Sekarang</button>
                                                </div>
                                            </div>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="kebersihan_ruangan" class="font-weight-bold">Kebersihan Ruangan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kebersihan_ruangan') is-invalid @enderror" 
                                                    id="kebersihan_ruangan" name="kebersihan_ruangan" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ (old('kebersihan_ruangan') ?? $areaProses->kebersihan_ruangan) == 'OK' ? 'selected' : '' }}>
                                                    ✓ OK
                                                </option>
                                                <option value="Kotor" {{ (old('kebersihan_ruangan') ?? $areaProses->kebersihan_ruangan) == 'Kotor' ? 'selected' : '' }}>
                                                    ✗ Kotor
                                                </option>
                                            </select>
                                            @error('kebersihan_ruangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="kebersihan_karyawan" class="font-weight-bold">Kebersihan Karyawan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kebersihan_karyawan') is-invalid @enderror" 
                                                    id="kebersihan_karyawan" name="kebersihan_karyawan" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ (old('kebersihan_karyawan') ?? $areaProses->kebersihan_karyawan) == 'OK' ? 'selected' : '' }}>
                                                    ✓ OK
                                                </option>
                                                <option value="Kotor" {{ (old('kebersihan_karyawan') ?? $areaProses->kebersihan_karyawan) == 'Kotor' ? 'selected' : '' }}>
                                                    ✗ Kotor
                                                </option>
                                            </select>
                                            @error('kebersihan_karyawan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                            
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="pemeriksaan_suhu_ruang" class="font-weight-bold">Pemeriksaan Suhu Ruang <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('pemeriksaan_suhu_ruang') is-invalid @enderror" 
                                                   id="pemeriksaan_suhu_ruang" 
                                                   name="pemeriksaan_suhu_ruang" 
                                                   value="{{ old('pemeriksaan_suhu_ruang') ?? $areaProses->pemeriksaan_suhu_ruang }}"
                                                   placeholder="Masukkan suhu ruang (°C)"
                                                   required>
                                            @error('pemeriksaan_suhu_ruang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3" id="kondisi_barang_container">
                                        <div class="form-group">
                                            <label for="kondisi_barang" class="font-weight-bold">Kondisi Barang <span class="text-danger">*</span></label>
                                            <select name="kondisi_barang" id="kondisi_barang" class="form-control @error('kondisi_barang') is-invalid @enderror">
                                                <option value="">Pilih Kondisi</option>
                                                <option value="OK" {{ old('kondisi_barang', $areaProses->kondisi_barang) == 'OK' ? 'selected' : '' }}>✓ OK</option>
                                                <option value="NOT OK" {{ old('kondisi_barang', $areaProses->kondisi_barang) == 'NOT OK' ? 'selected' : '' }}>✗ Tidak OK</option>
                                            </select>
                                            @error('kondisi_barang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @php
                                        $suhuRmValue = old('suhu_rm', $areaProses->suhu_rm);
                                        $suhuRmList = is_array($suhuRmValue) ? $suhuRmValue : (is_string($suhuRmValue) && trim($suhuRmValue) !== '' ? array_map('trim', explode(',', $suhuRmValue)) : ['']);
                                        if (empty($suhuRmList)) { $suhuRmList = ['']; }
                                    @endphp
                                    <div class="col-md-3" id="suhu_rm_container">
                                        <div class="form-group mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="suhu_rm" class="font-weight-bold mb-0">Suhu RM <span class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-xs btn-outline-success" id="btn-add-suhu-rm-edit" title="Tambah Suhu RM">
                                                    <i class="fas fa-plus"></i> Tambah Suhu
                                                </button>
                                            </div>
                                            <div id="suhu-rm-wrapper-edit">
                                                @foreach($suhuRmList as $index => $suhuRmItem)
                                                    <div class="input-group mb-2 suhu-rm-item-edit">
                                                        <input type="text" 
                                                               class="form-control suhu-rm-input-edit @error('suhu_rm') is-invalid @enderror" 
                                                               name="suhu_rm[]" 
                                                               value="{{ $suhuRmItem }}"
                                                               placeholder="Suhu RM">
                                                        <div class="input-group-append remove-suhu-rm-wrapper-edit" style="{{ count($suhuRmList) > 1 ? '' : 'display:none;' }}">
                                                            <button type="button" class="btn btn-outline-danger btn-remove-suhu-rm-edit" title="Hapus Suhu RM">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('suhu_rm')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                    
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ketidaksesuaian" class="font-weight-bold">Ketidaksesuaian</label>
                                            <textarea class="form-control @error('ketidaksesuaian') is-invalid @enderror" 
                                                      id="ketidaksesuaian" 
                                                      name="ketidaksesuaian" 
                                                      rows="3"
                                                      placeholder="Masukkan keterangan ketidaksesuaian jika ada">{{ old('ketidaksesuaian') ?? $areaProses->ketidaksesuaian }}</textarea>
                                            @error('ketidaksesuaian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tindakan_koreksi" class="font-weight-bold">Tindakan Koreksi</label>
                                            <textarea class="form-control @error('tindakan_koreksi') is-invalid @enderror" 
                                                      id="tindakan_koreksi" 
                                                      name="tindakan_koreksi" 
                                                      rows="3"
                                                      placeholder="Masukkan tindakan koreksi yang dilakukan">{{ old('tindakan_koreksi') ?? $areaProses->tindakan_koreksi }}</textarea>
                                            @error('tindakan_koreksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="">
                                        <button type="submit" class="btn btn-warning btn-sm mr-2">
                                            <i class="fas fa-save"></i>
                                            @isset($twoHour) Simpan Data per 2 Jam (buat riwayat) @else Update Data @endisset
                                        </button>
                                        <a href="{{ ($is_audit ?? false) ? route('area-proses.audit') : route('area-proses.index') }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                        <div>
                                            <!-- <a href="{{ route('area-proses.show', $areaProses->uuid) }}" class="btn btn-info mr-2">
                                                <i class="fas fa-eye mr-2"></i>Lihat Detail
                                            </a> -->
                                        </div>
                                    </div>
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
function setNowTanggal() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    document.getElementById('tanggal').value = formattedDateTime;
}

function setNowJam() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const formattedTime = `${hours}:${minutes}`;
    document.getElementById('jam').value = formattedTime;
}
</script>

<script>
    $(document).ready(function() {
        const kondisiBarangContainer = $('#kondisi_barang_container');
        const suhuRMContainer = $('#suhu_rm_container');

        function updateEditSuhuRmRemoveButtons() {
            const items = $('#suhu-rm-wrapper-edit .suhu-rm-item-edit');
            items.each(function() {
                if (items.length > 1) {
                    $(this).find('.remove-suhu-rm-wrapper-edit').show();
                } else {
                    $(this).find('.remove-suhu-rm-wrapper-edit').hide();
                }
            });
        }

        $('#btn-add-suhu-rm-edit').on('click', function() {
            const newItem = `
                <div class="input-group mb-2 suhu-rm-item-edit">
                    <input type="text" class="form-control suhu-rm-input-edit" name="suhu_rm[]" placeholder="Suhu RM">
                    <div class="input-group-append remove-suhu-rm-wrapper-edit">
                        <button type="button" class="btn btn-outline-danger btn-remove-suhu-rm-edit" title="Hapus Suhu RM">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#suhu-rm-wrapper-edit').append(newItem);
            updateEditSuhuRmRemoveButtons();
        });

        $('#suhu-rm-wrapper-edit').on('click', '.btn-remove-suhu-rm-edit', function() {
            if ($('#suhu-rm-wrapper-edit .suhu-rm-item-edit').length > 1) {
                $(this).closest('.suhu-rm-item-edit').remove();
                updateEditSuhuRmRemoveButtons();
            }
        });

        function toggleDynamicFields() {
            let selectedText = $('#id_area_select_area_proses option:selected').text().trim().toLowerCase();
            let cleanText = selectedText.replace(/[\s\-_]/g, '');

            if (cleanText.includes('chillroom') || cleanText.includes('seasoning')) {
                kondisiBarangContainer.show();
                $('#kondisi_barang').attr('required', 'required');
            } else {
                kondisiBarangContainer.hide();
                $('#kondisi_barang').removeAttr('required');
            }

            if (cleanText.includes('chillroom')) {
                suhuRMContainer.show();
                $('.suhu-rm-input-edit').first().attr('required', 'required');
            } else {
                suhuRMContainer.hide();
                $('.suhu-rm-input-edit').removeAttr('required');
            }
        }

        $('#id_area_select_area_proses').on('change', toggleDynamicFields);

        toggleDynamicFields();
        updateEditSuhuRmRemoveButtons();
    });
</script>
@endpush
@endsection