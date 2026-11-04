@extends('layouts.app')

@section('container')
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
                        <li class="breadcrumb-item"><a href="{{ route('label-pemeriksaan.index') }}">Pemeriksaan Labelisasi Start Up</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-info-circle mr-1"></i> {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">Baris Pemeriksaan</h3>
                    <div class="card-tools ml-auto">
                        @if($canEditQcVerification ?? false)
                            <a href="#qc-verification-section" class="btn btn-warning btn-sm font-weight-bold mr-2">
                                <i class="fas fa-clipboard-check mr-1"></i> Verifikasi QC
                            </a>
                        @endif
                        <a href="{{ route('label-pemeriksaan.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
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
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle"><strong>{{ $detail->produk->nama_produk ?? '-' }}</strong></td>
                                    <td class="text-center align-middle">
                                        @if($detail->berat)
                                            <span class="badge badge-secondary">{{ rtrim(rtrim(number_format($detail->berat, 2), '0'), '.') }} gram</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info">{{ $detail->label_produk ?? '-' }}</span>
                                    </td>
                                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($detail->jam)->format('H:i') }}</td>
                                    <td class="text-center align-middle">
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
                                    <td class="text-center align-middle">
                                        @if($detail->hasil_pengecekan === 'OK')
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>OK</span>
                                        @elseif($detail->hasil_pengecekan === 'Tidak OK')
                                            <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Tidak OK</span>
                                        @else
                                            <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i>Belum Diverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <strong>{{ $detail->nama_operator ?? '-' }}</strong>
                                        @if($detail->ttd_operator)
                                            <br><img src="{{ $detail->ttd_operator }}" class="preview-img" style="max-height: 50px; display: block; margin: 5px auto; border: 1px solid #efefef; cursor: pointer;" data-title="TTD Operator - {{ $detail->nama_operator }}">
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($detail->nama_qc)
                                            <strong>{{ $detail->nama_qc }}</strong>
                                            @if($detail->ttd_qc)
                                                <br><img src="{{ $detail->ttd_qc }}" class="preview-img" style="max-height: 50px; display: block; margin: 5px auto; border: 1px solid #efefef; cursor: pointer;" data-title="TTD QC - {{ $detail->nama_qc }}">
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">{{ $detail->tindakan_koreksi ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Verifikasi QC Inspector (Khusus Role QC / Admin / Superadmin) -->
            @if($canEditQcVerification ?? false)
            <div class="card card-warning card-outline mt-4 shadow-sm mb-5" id="qc-verification-section">
                <div class="card-header bg-warning py-2">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-clipboard-check mr-2"></i> Form Input / Update Verifikasi QC Inspector
                    </h3>
                </div>
                <form action="{{ route('label-pemeriksaan.qc-verification', $item->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-info py-2 mb-3">
                            <i class="fas fa-info-circle mr-1"></i> Periksa kesesuaian Nama Produk, Berat, Label (PL/MC), Pukul, Foto Kode Produksi, dan PIC Operator di atas, kemudian tentukan <strong>Hasil Verifikasi (OK / Tidak OK)</strong> untuk setiap baris pemeriksaan.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Produk</th>
                                        <th>Berat</th>
                                        <th>Label</th>
                                        <th>Pukul</th>
                                        <th>PIC Operator</th>
                                        <th style="width: 260px;">Verifikasi Hasil QC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->details as $detail)
                                    <tr>
                                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle"><strong>{{ $detail->produk->nama_produk ?? '-' }}</strong></td>
                                        <td class="text-center align-middle">{{ $detail->berat ? rtrim(rtrim(number_format($detail->berat, 2), '0'), '.') . ' g' : '-' }}</td>
                                        <td class="text-center align-middle"><span class="badge badge-info">{{ $detail->label_produk ?? '-' }}</span></td>
                                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($detail->jam)->format('H:i') }}</td>
                                        <td class="text-center align-middle">{{ $detail->nama_operator ?? '-' }}</td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center align-items-center" style="gap: 1.5rem;">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="hasil_ok_{{ $detail->id }}" name="hasil_pengecekan[{{ $detail->id }}]" value="OK" class="custom-control-input" {{ $detail->hasil_pengecekan === 'OK' ? 'checked' : '' }} required>
                                                    <label class="custom-control-label text-success font-weight-bold" for="hasil_ok_{{ $detail->id }}" style="cursor: pointer;">
                                                        <i class="fas fa-check-circle mr-1"></i> OK
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="hasil_not_ok_{{ $detail->id }}" name="hasil_pengecekan[{{ $detail->id }}]" value="Tidak OK" class="custom-control-input" {{ $detail->hasil_pengecekan === 'Tidak OK' ? 'checked' : '' }} required>
                                                    <label class="custom-control-label text-danger font-weight-bold" for="hasil_not_ok_{{ $detail->id }}" style="cursor: pointer;">
                                                        <i class="fas fa-times-circle mr-1"></i> Tidak OK
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-right py-2">
                        <button type="submit" class="btn btn-success shadow-sm font-weight-bold px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                        </button>
                    </div>
                </form>
            </div>
            @endif
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
