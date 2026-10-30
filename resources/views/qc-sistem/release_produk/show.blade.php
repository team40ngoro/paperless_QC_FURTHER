@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Release Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('release-produk.index') }}">Release Produk</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- General Info -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <h3 class="profile-username text-center">{{ $item->produk->nama_produk ?? '-' }}</h3>
                            <p class="text-muted text-center">{{ $item->plan->nama_plan ?? '-' }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Tanggal & Jam</b> <a class="float-right">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }} {{ $item->jam }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Shift</b> <a class="float-right">{{ $item->shift->shift ?? '-' }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Dibuat Oleh</b> <a class="float-right">{{ $item->user->name ?? '-' }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> 
                                    <span class="float-right badge badge-{{ $item->status == 'Release' ? 'success' : 'danger' }}">{{ $item->status }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Parameter Detail -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Parameter Teknis</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-thermometer-half mr-1"></i> Suhu IQF</strong>
                            <p class="text-muted">{{ $item->suhu_iqf }} ℃</p>
                            <hr>
                            <strong><i class="fas fa-thermometer-full mr-1"></i> Suhu Produk</strong>
                            <p class="text-muted">{{ $item->suhu_produk }} ℃</p>
                            <hr>
                            <strong><i class="fas fa-stopwatch mr-1"></i> Lama Pembekuan</strong>
                            <p class="text-muted">{{ $item->lama_pembekuan }} Menit</p>
                            <hr>
                            <strong><i class="fas fa-boxes mr-1"></i> Jumlah Box</strong>
                            <p class="text-muted">{{ $item->jumlah_box }} Box</p>
                            <hr>
                            <strong><i class="fas fa-weight mr-1"></i> Data Berat Packing Sekunder (g)</strong>
                            <ul class="list-unstyled mt-2">
                                @if(is_array($item->berat_packing_sekunder))
                                    @foreach($item->berat_packing_sekunder as $berat)
                                    <li><i class="fas fa-caret-right text-primary"></i> {{ $berat }} g</li>
                                    @endforeach
                                @else
                                    <li><i class="fas fa-caret-right text-primary"></i> {{ $item->berat_packing_sekunder }} g</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Documentation -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Dokumentasi Foto</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <label>Kemasan Sekunder</label>
                                    <div class="img-thumbnail mb-2" style="height: 250px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                            @php
                                                $pathSekunder = storage_path('app/public/' . $item->foto_kemasan_sekunder);
                                                $dataSekunder = file_exists($pathSekunder) ? base64_encode(file_get_contents($pathSekunder)) : null;
                                            @endphp
                                            @if($dataSekunder)
                                                <img src="data:image/jpeg;base64,{{ $dataSekunder }}" class="preview-img" style="max-width: 100%; max-height: 100%; cursor: pointer;" data-title="Kemasan Sekunder">
                                            @else
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            @endif
                                    </div>
                                </div>
                                <div class="col-md-6 text-center">
                                    <label>Kemasan Primer</label>
                                    <div class="img-thumbnail mb-2" style="height: 250px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                            @php
                                                $pathPrimer = storage_path('app/public/' . $item->foto_kemasan_primer);
                                                $dataPrimer = file_exists($pathPrimer) ? base64_encode(file_get_contents($pathPrimer)) : null;
                                            @endphp
                                            @if($dataPrimer)
                                                <img src="data:image/jpeg;base64,{{ $dataPrimer }}" class="preview-img" style="max-width: 100%; max-height: 100%; cursor: pointer;" data-title="Kemasan Primer">
                                            @else
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Findings -->
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Keterangan & Temuan</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-clipboard-list mr-1"></i> Kondisi Kemasan Primer</strong>
                            <p class="text-muted">{{ $item->kondisi_kemasan_primer }}</p>
                            <hr>
                            <strong><i class="fas fa-comment mr-1"></i> Keterangan</strong>
                            <p class="text-muted">{{ $item->keterangan ?? '-' }}</p>
                            <hr>
                            <strong><i class="fas fa-tools mr-1"></i> Tindakan Koreksi</strong>
                            <p class="text-muted">{{ $item->tindakan_koreksi ?? '-' }}</p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('release-produk.index') }}" class="btn btn-secondary">Kembali</a>
                            <a href="{{ route('release-produk.edit', $item->uuid) }}" class="btn btn-warning">Edit</a>
                        </div>
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
