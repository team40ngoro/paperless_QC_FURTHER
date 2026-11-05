@extends('layouts.app')

@section('container')
@php
    $currentUser = auth()->user();
    $canVerifyKedatangan = $currentUser
        && in_array((int) $currentUser->id_role, [1, 3, 5], true)
        && ($currentUser->role === 'superadmin' || (int) $currentUser->id_plan === (int) $item->id_plan);
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pemeriksaan Bahan Kemas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-bahan-kemas.index') }}">Pemeriksaan Bahan Kemas</a></li>
                        <li class="breadcrumb-item active">Detail</li>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-eye text-primary mr-2"></i>
                                    Detail Pemeriksaan Bahan Kemas
                                </h3>
                                <a href="{{ route('pemeriksaan-bahan-kemas.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <div class="form-control bg-light">
                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam</label>
                                        <div class="form-control bg-light">
                                            {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Shift</label>
                                        <div class="form-control bg-light">
                                            {{ $item->shift->shift ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Kemasan</label>
                                        <div class="form-control bg-light">
                                            {{ $item->nama_kemasan ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Produksi</label>
                                        <div class="form-control bg-light">
                                            {{ $item->kode_produksi ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kondisi Bahan Kemasan</label>
                                        <div class="form-control bg-light">
                                            {{ $item->kondisi_bahan_kemasan ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <div class="form-control bg-light" style="height: auto; min-height: 60px;">
                                            {{ $item->keterangan ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verifikasi Kedatangan Bahan Kemas -->
                    <div class="card card-success card-outline mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box-open text-success mr-2"></i>
                                Verifikasi Kedatangan Bahan Kemas
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            <div class="mb-2">
                                <strong>Status:</strong>
                                @if($item->verifikasi_kedatangan === 'OK')
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> v - Oke</span>
                                @elseif($item->verifikasi_kedatangan === 'NOT_OK')
                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> x - Tidak Oke</span>
                                @else
                                    <span class="badge badge-light border">Belum diverifikasi</span>
                                @endif
                            </div>

                            <div class="small text-muted mb-3">
                                <div><strong>v = Oke:</strong> sesuai spesifikasi.</div>
                                <div><strong>x = Tidak Oke:</strong> tidak sesuai spesifikasi.</div>
                            </div>

                            @if($canVerifyKedatangan)
                                <form action="{{ route('pemeriksaan-bahan-kemas.verifikasi-kedatangan.store', $item->uuid) }}" method="POST">
                                    @csrf
                                    <div class="d-flex flex-wrap align-items-center mb-2">
                                        <div class="custom-control custom-checkbox mr-4">
                                            <input type="checkbox" class="custom-control-input" id="verifikasiKedatanganOk" data-target="verifikasiKedatangan"
                                                data-value="OK" {{ old('verifikasi_kedatangan', $item->verifikasi_kedatangan) === 'OK' ? 'checked' : '' }}>
                                            <label class="custom-control-label text-success font-weight-bold" for="verifikasiKedatanganOk">
                                                v - Oke
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="verifikasiKedatanganNotOk" data-target="verifikasiKedatangan"
                                                data-value="NOT_OK" {{ old('verifikasi_kedatangan', $item->verifikasi_kedatangan) === 'NOT_OK' ? 'checked' : '' }}>
                                            <label class="custom-control-label text-danger font-weight-bold" for="verifikasiKedatanganNotOk">
                                                x - Tidak Oke
                                            </label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="verifikasi_kedatangan" id="verifikasiKedatanganValue"
                                        value="{{ old('verifikasi_kedatangan', $item->verifikasi_kedatangan) }}">
                                    @error('verifikasi_kedatangan')
                                        <small class="text-danger d-block mb-2">{{ $message }}</small>
                                    @enderror
                                    <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                        <i class="fas fa-save mr-1"></i> Simpan Verifikasi
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-light border mb-0">
                                    <i class="fas fa-lock mr-1"></i> Hanya role QC yang dapat mengubah verifikasi ini.
                                </div>
                            @endif

                            @if($item->verifikasi_kedatangan)
                                <div class="small text-muted mt-3">
                                    Diverifikasi oleh <strong>{{ $item->verifikasiKedatanganBy->name ?? '-' }}</strong>
                                    pada {{ $item->verifikasi_kedatangan_at ? $item->verifikasi_kedatangan_at->format('d/m/Y H:i') : '-' }}.
                                </div>
                            @endif
                        </div>
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
        const checkboxes = document.querySelectorAll('#verifikasiKedatanganOk, #verifikasiKedatanganNotOk');
        const hiddenInput = document.getElementById('verifikasiKedatanganValue');

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    checkboxes.forEach(function (other) {
                        if (other !== checkbox) {
                            other.checked = false;
                        }
                    });
                    hiddenInput.value = this.dataset.value;
                } else {
                    hiddenInput.value = '';
                }
            });
        });
    });
</script>
@endpush
