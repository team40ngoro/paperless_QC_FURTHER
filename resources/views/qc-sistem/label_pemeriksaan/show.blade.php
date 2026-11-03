@extends('layouts.app')

@section('container')
@php
    $currentUser = auth()->user();
    $canVerifyBahanKemas = $currentUser
        && in_array((int) $currentUser->id_role, [1, 3, 5], true)
        && ($currentUser->role === 'superadmin' || (int) $currentUser->id_plan === (int) $item->id_plan);
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pemeriksaan Labelisasi Start Up</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('label-pemeriksaan.index') }}">Pemeriksaan Labelisasi Start Up</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Header Info -->
            <div class="card card-primary card-outline">
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tr>
                            <th style="width: 200px;" class="bg-light">Tanggal</th>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <th style="width: 200px;" class="bg-light">Shift</th>
                            <td>{{ $item->shift->shift ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Plant</th>
                            <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                            <th class="bg-light">Dibuat Oleh</th>
                            <td>{{ $item->user->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="card card-info card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title">Baris Pemeriksaan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Berat</th>
                                    <th>Label</th>
                                    <th>Pukul</th>
                                    <th>Foto Kode Produksi</th>
                                    <th>Hasil</th>
                                    <th>PIC Operator</th>
                                    <th>PIC QC</th>
                                    <th>Tindakan Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->details as $detail)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($detail->berat)
                                            <span class="badge badge-secondary">{{ rtrim(rtrim(number_format($detail->berat, 2), '0'), '.') }} gram</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $detail->label_produk ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($detail->jam)->format('H:i') }}</td>
                                    <td class="text-center">
                                        @if($detail->foto_kode_produksi)
                                            @php
                                                $path = storage_path('app/public/' . $detail->foto_kode_produksi);
                                                $base64 = is_file($path) ? base64_encode(file_get_contents($path)) : null;
                                            @endphp
                                            @if($base64)
                                                <img src="data:image/jpeg;base64,{{ $base64 }}" class="preview-img" style="max-width: 120px; cursor: pointer; border: 1px solid #ddd; border-radius: 4px;" data-title="Foto Kode Produksi - {{ $detail->produk->nama_produk }}">
                                            @else
                                                <span class="text-muted small">File tidak ditemukan</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $detail->hasil_pengecekan == 'OK' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $detail->hasil_pengecekan }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $detail->nama_operator }}</strong><br>
                                        <img src="{{ $detail->ttd_operator }}" class="preview-img" style="max-height: 50px; display: block; margin: 5px auto; border: 1px solid #efefef; cursor: pointer;" data-title="TTD Operator - {{ $detail->nama_operator }}">
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $detail->nama_qc }}</strong><br>
                                        <img src="{{ $detail->ttd_qc }}" class="preview-img" style="max-height: 50px; display: block; margin: 5px auto; border: 1px solid #efefef; cursor: pointer;" data-title="TTD QC - {{ $detail->nama_qc }}">
                                    </td>
                                    <td>{{ $detail->tindakan_koreksi ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                        @if($item->verifikasi_bahan_kemas === 'OK')
                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> v - Oke</span>
                        @elseif($item->verifikasi_bahan_kemas === 'NOT_OK')
                            <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> x - Tidak Oke</span>
                        @else
                            <span class="badge badge-light border">Belum diverifikasi</span>
                        @endif
                    </div>

                    <div class="small text-muted mb-3">
                        <div><strong>v = Oke:</strong> sesuai spesifikasi.</div>
                        <div><strong>x = Tidak Oke:</strong> tidak sesuai spesifikasi.</div>
                    </div>

                    @if($canVerifyBahanKemas)
                        <form action="{{ route('label-pemeriksaan.verifikasi-bahan-kemas.store', $item->uuid) }}" method="POST">
                            @csrf
                            <div class="d-flex flex-wrap align-items-center mb-2">
                                <div class="custom-control custom-checkbox mr-4">
                                    <input type="checkbox" class="custom-control-input" id="verifikasiBahanKemasOk" data-target="verifikasiBahanKemas"
                                        data-value="OK" {{ old('verifikasi_bahan_kemas', $item->verifikasi_bahan_kemas) === 'OK' ? 'checked' : '' }}>
                                    <label class="custom-control-label text-success font-weight-bold" for="verifikasiBahanKemasOk">
                                        v - Oke
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="verifikasiBahanKemasNotOk" data-target="verifikasiBahanKemas"
                                        data-value="NOT_OK" {{ old('verifikasi_bahan_kemas', $item->verifikasi_bahan_kemas) === 'NOT_OK' ? 'checked' : '' }}>
                                    <label class="custom-control-label text-danger font-weight-bold" for="verifikasiBahanKemasNotOk">
                                        x - Tidak Oke
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="verifikasi_bahan_kemas" id="verifikasiBahanKemasValue"
                                value="{{ old('verifikasi_bahan_kemas', $item->verifikasi_bahan_kemas) }}">
                            @error('verifikasi_bahan_kemas')
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

                    @if($item->verifikasi_bahan_kemas)
                        <div class="small text-muted mt-3">
                            Diverifikasi oleh <strong>{{ $item->bahanKemasVerifier->name ?? '-' }}</strong>
                            pada {{ $item->verifikasi_bahan_kemas_at ? $item->verifikasi_bahan_kemas_at->format('d/m/Y H:i') : '-' }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for Image Preview -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border: none; background-color: rgba(0, 0, 0, 0.85);">
                <div class="modal-header border-0">
                    <h4 class="modal-title text-white" id="modalTitle">Preview Foto</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" style="max-width: 100%; max-height: 85vh; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,.5);">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('#verifikasiBahanKemasOk, #verifikasiBahanKemasNotOk');
        const hiddenInput = document.getElementById('verifikasiBahanKemasValue');

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

    $(document).ready(function() {
        $('.preview-img').on('click', function() {
            const src = $(this).attr('src');
            const title = $(this).data('title');
            $('#modalImage').attr('src', src);
            $('#modalTitle').text(title);
            $('#imageModal').modal('show');
        });
    });
</script>
@endpush
