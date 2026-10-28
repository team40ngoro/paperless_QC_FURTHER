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
