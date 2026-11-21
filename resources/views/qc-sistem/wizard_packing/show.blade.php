@extends('layouts.app')
@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp
@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-boxes text-primary"></i> Detail Pengemasan Packing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route($pembekuan->is_audit ? 'wizards.packing.audit' : 'wizards.packing.index') }}">Pengemasan Packing</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    {{-- ===== HEADER INFO ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card bg-gradient-primary shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="mb-1 font-weight-bold text-white">
                                            {{ $pembekuan->pengemasanProduk->produk->nama_produk ?? 'N/A' }} 
                                            @if($pembekuan->pengemasanProduk && $pembekuan->pengemasanProduk->is_kombinasi)
                                                + {{ $pembekuan->pengemasanProduk->produk2->nama_produk ?? 'Produk 2' }}
                                                <span class="badge badge-warning text-dark ml-2" style="font-size: 13px;"><i class="fas fa-boxes mr-1"></i> Box Kombinasi (2 Produk)</span>
                                            @endif
                                        </h3>
                                        <div class="d-flex flex-wrap" style="gap: 15px;">
                                            <span class="badge badge-light p-2"><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d F Y') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-clock mr-1"></i> {{ $pembekuan->jam }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-weight mr-1"></i>Produk 1: {{ $pembekuan->pengemasanProduk->berat ?? '-' }} gram</span>
                                            @if($pembekuan->pengemasanProduk && $pembekuan->pengemasanProduk->is_kombinasi)
                                            <span class="badge badge-warning p-2 text-dark"><i class="fas fa-weight mr-1"></i>Produk 2: {{ $pembekuan->pengemasanProduk->berat_produk_2 ?? '-' }} gram</span>
                                            @endif
                                            <span class="badge badge-light p-2"><i class="fas fa-layer-group mr-1"></i>Shift {{ $pembekuan->shift->shift ?? 'N/A' }}</span>
                                            @if($pembekuan->pengemasanProduk)
                                            <span class="badge badge-light p-2"><i class="fas fa-barcode mr-1"></i>Kode Produksi 1: {{ $pembekuan->pengemasanProduk->kode_produksi ?? 'N/A' }}</span>
                                            @if($pembekuan->pengemasanProduk->is_kombinasi)
                                            <span class="badge badge-warning p-2 text-dark"><i class="fas fa-barcode mr-1"></i>Kode Produksi 2: {{ $pembekuan->pengemasanProduk->kode_produksi_2 ?? 'N/A' }}</span>
                                            @endif
                                            <span class="badge badge-light p-2"><i class="fas fa-user mr-1"></i>Dibuat Oleh: {{ $pembekuan->user->name ?? 'N/A' }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-calendar-times mr-1"></i>Exp 1: {{ $pembekuan->pengemasanProduk->tanggal_expired ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired)->format('d-m-Y') : 'N/A' }}</span>
                                            @if($pembekuan->pengemasanProduk->is_kombinasi && $pembekuan->pengemasanProduk->tanggal_expired_2)
                                            <span class="badge badge-warning p-2 text-dark"><i class="fas fa-calendar-times mr-1"></i>Exp 2: {{ \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired_2)->format('d-m-Y') }}</span>
                                            @endif
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            @if($isQcCompleted ?? false)
                                                <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> Verifikasi QC Lengkap</span>
                                            @else
                                                <span class="badge badge-warning p-2 text-dark"><i class="fas fa-hourglass-half mr-1"></i> Menunggu Input Verifikasi QC</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        <a href="{{ route($pembekuan->is_audit ? 'wizards.packing.audit' : 'wizards.packing.index') }}" class="btn btn-light btn-sm mr-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                        @if($canEditQcVerification ?? false)
                                            <a href="#qc-verification-section" class="btn btn-warning btn-sm font-weight-bold mr-2">
                                                <i class="fas fa-clipboard-check mr-1"></i> {{ ($isQcCompleted ?? false) ? 'Update Verifikasi QC' : 'Input Verifikasi QC' }}
                                            </a>
                                        @endif
                                        @if(auth()->user()->hasPermissionTo('edit-wizard-packing') && in_array((int)(auth()->user()->id_role ?? 0), [1, 2, 5], true))
                                            <a href="{{ route('wizards.packing.edit', $pembekuan->uuid) }}" class="btn btn-outline-light btn-sm">
                                                <i class="fas fa-edit mr-1"></i> Edit Data Produksi
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== STEP-BY-STEP DETAILS ===== --}}
                    <div class="col-12">

                        {{-- STEP 1: PENGEMASAN PRODUK --}}
                        @if($pembekuan->pengemasanProduk)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-snowflake text-primary mr-1"></i> Langkah 1: Pembekuan Produk</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"><strong>Standar Suhu Produk IQF:</strong><p>{{ $pembekuan->pengemasanProduk->std_suhu_produk_iqf ?: '-18°C' }}</p></div>
                                    <div class="col-md-3"><strong>Waktu Pengecekan After IQF:</strong><p>{{ $pembekuan->pengemasanProduk->waktu_awal_packing ?: '-' }}</p></div>
                                    <div class="col-md-3"><strong>Suhu IQF (Produksi):</strong><p>{{ $pembekuan->pengemasanProduk->suhu_iqf !== null && $pembekuan->pengemasanProduk->suhu_iqf !== '' ? $pembekuan->pengemasanProduk->suhu_iqf . ' °C' : '-' }}</p></div>
                                    <div class="col-md-3">
                                        <strong>Suhu Aktual Produk Pembekuan (QC):</strong>
                                        <p>
                                            @php
                                                $suhus = explode(',', $pembekuan->pengemasanProduk->aktual_suhu_produk ?? '');
                                                $suhus = array_filter(array_map('trim', $suhus), fn($v) => $v !== '');
                                                echo !empty($suhus) ? implode(' °C, ', $suhus) . ' °C' : '-';
                                            @endphp
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 2: PENGEMASAN PLASTIK --}}
                        @if($pembekuan->pengemasanPlastik)
                        <div class="card card-outline card-success mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-shopping-bag text-success mr-1"></i> Langkah 2: Pengemasan Plastik</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"><strong>Identitas Produk:</strong><p>{{ $pembekuan->pengemasanPlastik->identitas_produk }}</p></div>
                                    <div class="col-md-3">
                                        <strong>Nomor MD:</strong>
                                        @if($pembekuan->pengemasanPlastik->nomor_md)
                                            <div class="mt-1">
                                                <a href="#" data-toggle="modal" data-target="#modalNomorMd{{ $pembekuan->pengemasanPlastik->id }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-image mr-1"></i> Lihat Foto
                                                </a>
                                            </div>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalNomorMd{{ $pembekuan->pengemasanPlastik->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Nomor MD</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ asset($assetPath . 'storage/' . $pembekuan->pengemasanPlastik->nomor_md) }}" alt="Nomor MD" style="max-width:100%;max-height:70vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </div>
                                    <div class="col-md-3"><strong>Kode Kemasan:</strong><p>{{ $pembekuan->pengemasanPlastik->kode_kemasan_plastik }}</p></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>Kesesuaian Label Plastik:</strong><p><span class="badge badge-{{ $pembekuan->pengemasanPlastik->kesesuaian_label_plastik == 'OK' ? 'success' : 'danger' }} p-2">{{ $pembekuan->pengemasanPlastik->kesesuaian_label_plastik }}</span></p></div>
                                    <div class="col-md-3"><strong>Proses Penimbangan:</strong><p>{{ $pembekuan->pengemasanPlastik->proses_penimbangan }}</p></div>
                                    <div class="col-md-3"><strong>Proses Sealing:</strong><p>{{ $pembekuan->pengemasanPlastik->proses_sealing }}</p></div>
                                    <div class="col-md-3"><strong>Kekuatan Seal:</strong><p><span class="badge badge-{{ $pembekuan->pengemasanPlastik->kekuatan_seal == '✔' ? 'success' : 'danger' }} p-2">{{ $pembekuan->pengemasanPlastik->kekuatan_seal }}</span></p></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>Line:</strong><p>{{ $pembekuan->pengemasanPlastik->line ?: '-' }}</p></div>
                                    <div class="col-md-3"><strong>Waktu Pengemasan:</strong><p>{{ $pembekuan->pengemasanPlastik->waktu_pengemasan ?: '-' }}</p></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 3: BERAT PRODUK BAG --}}
                        @if($semuaBeratProdukBag && count($semuaBeratProdukBag) > 0)
                        <div class="card card-outline card-warning mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-weight-hanging text-warning mr-1"></i> Langkah 3: Berat Produk Bag</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-striped m-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Line</th>
                                            <th>Data Bag</th>
                                            <th>Aktual (gr)</th>
                                            <th>Rata-rata (gr)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($semuaBeratProdukBag as $bag)
                                        <tr>
                                            <td>{{ $bag->line }}</td>
                                            <td>{{ optional($bag->data_bag)->std_bag ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $berats = is_string($bag->berat_aktual) ? json_decode($bag->berat_aktual, true) : $bag->berat_aktual;
                                                    $avgBag = 0;
                                                    if (is_array($berats) && count($berats) > 0) {
                                                        $filtered = array_filter($berats, function($v) { return is_numeric($v); });
                                                        if (count($filtered) > 0) {
                                                            $sumBag = array_sum(array_map('floatval', $filtered));
                                                            $avgBag = $sumBag / count($filtered);
                                                        }
                                                    }
                                                @endphp
                                                @if(is_array($berats) && count($berats) > 0)
                                                    {{ implode(', ', $berats) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ is_array($berats) && count($berats) > 0 ? number_format($avgBag, 2, '.', '') : $bag->rata_rata_berat }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 4: BERAT PRODUK BOX --}}
                        @if($pembekuan->beratProdukBox)
                        <div class="card card-outline card-danger mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-cube text-danger mr-1"></i> Langkah 4: Berat Produk Box</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <strong>Data Box:</strong>
                                        <p>{{ optional($pembekuan->beratProdukBox->data_box)->std_box ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>Aktual (gr):</strong>
                                        <p>
                                            @php
                                                $beratsBox = is_string($pembekuan->beratProdukBox->berat_aktual) ? json_decode($pembekuan->beratProdukBox->berat_aktual, true) : $pembekuan->beratProdukBox->berat_aktual;
                                                $avgBox = 0;
                                                if (is_array($beratsBox) && count($beratsBox) > 0) {
                                                    $filteredBox = array_filter($beratsBox, function($v) { return is_numeric($v); });
                                                    if (count($filteredBox) > 0) {
                                                        $sum = array_sum(array_map('floatval', $filteredBox));
                                                        $avgBox = $sum / count($filteredBox);
                                                    }
                                                }
                                            @endphp
                                            @if(is_array($beratsBox) && count($beratsBox) > 0)
                                                {{ implode(', ', $beratsBox) }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Rata-rata Berat (gr):</strong>
                                        <p class="font-weight-bold">
                                            {{ is_array($beratsBox) && count($beratsBox) > 0 ? number_format($avgBox, 2, '.', '') : $pembekuan->beratProdukBox->rata_rata_berat }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 5: PENGEMASAN KARTON --}}
                        @if($pembekuan->pengemasanKarton)
                        <div class="card card-outline card-info mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-boxes text-info mr-1"></i> Langkah 5: Pengemasan Karton</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3"><strong>Jam : </strong><p>{{ $pembekuan->pengemasanKarton->jam }}</p></div>
                                    <div class="col-md-3"><strong>Kesesuaian Label Karton:</strong><p>{{ $pembekuan->pengemasanKarton->identitas_produk_pada_karton }}</p></div>
                                    <div class="col-md-3"><strong>Standar isi pack dalam karton:</strong><p>{{ $pembekuan->pengemasanKarton->standar_jumlah_karton }}</p></div>
                                    <div class="col-md-3"><strong>Aktual isi pack dalam karton:</strong><p>{{ $pembekuan->pengemasanKarton->aktual_jumlah_karton }}</p></div>
                                </div>
                                @php
                                    $isiPack = $pembekuan->pengemasanKarton->kesesuaian_isi_pack;
                                    if (is_string($isiPack)) $isiPack = json_decode($isiPack, true);
                                @endphp
                                @if(is_array($isiPack) && count($isiPack) > 0)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <strong>Kesesuaian Standar Isi Pack dengan Aktual:</strong>
                                        <p class="mb-0">
                                            @foreach($isiPack as $item)
                                                <span class="badge badge-{{ $item == '✔' ? 'success' : 'danger' }} p-2 mr-1">{{ $item }}</span>
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <hr>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Hasil Finish Good in Box / Pallet:</strong>
                                        <p class="font-weight-bold text-primary mb-0">{{ $pembekuan->pengemasanKarton->hasil_finish_good_box_pallet ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total Reject Kemasan:</strong>
                                        <p class="font-weight-bold text-danger mb-0">{{ $pembekuan->pengemasanKarton->total_reject_kemasan !== null ? $pembekuan->pengemasanKarton->total_reject_kemasan : '-' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 bg-light mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong class="text-danger"><i class="fas fa-times-circle mr-1"></i> Total Reject Produk:</strong>
                                                <span class="badge badge-danger px-2 py-1" style="font-size:0.95rem;">{{ $pembekuan->pengemasanKarton->total_reject_produk !== null ? $pembekuan->pengemasanKarton->total_reject_produk : '-' }}</span>
                                            </div>
                                            <div class="row text-center small">
                                                <div class="col-3"><strong>MP:</strong><br>{{ $pembekuan->pengemasanKarton->reject_produk_mp ?? '-' }}</div>
                                                <div class="col-3"><strong>Cooking:</strong><br>{{ $pembekuan->pengemasanKarton->reject_produk_cooking ?? '-' }}</div>
                                                <div class="col-3"><strong>IQF:</strong><br>{{ $pembekuan->pengemasanKarton->reject_produk_iqf ?? '-' }}</div>
                                                <div class="col-3"><strong>Packing:</strong><br>{{ $pembekuan->pengemasanKarton->reject_produk_packing ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 bg-light mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong class="text-warning"><i class="fas fa-sync-alt mr-1"></i> Total Rework Produk:</strong>
                                                <span class="badge badge-warning text-dark px-2 py-1" style="font-size:0.95rem;">{{ $pembekuan->pengemasanKarton->total_rework_produk !== null ? $pembekuan->pengemasanKarton->total_rework_produk : '-' }}</span>
                                            </div>
                                            <div class="row text-center small">
                                                <div class="col-3"><strong>MP:</strong><br>{{ $pembekuan->pengemasanKarton->rework_produk_mp ?? '-' }}</div>
                                                <div class="col-3"><strong>Cooking:</strong><br>{{ $pembekuan->pengemasanKarton->rework_produk_cooking ?? '-' }}</div>
                                                <div class="col-3"><strong>IQF:</strong><br>{{ $pembekuan->pengemasanKarton->rework_produk_iqf ?? '-' }}</div>
                                                <div class="col-3"><strong>Packing:</strong><br>{{ $pembekuan->pengemasanKarton->rework_produk_packing ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endif

                        {{-- STEP 6: DOKUMENTASI --}}
                        <div class="card card-outline card-dark mb-5 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-camera text-dark mr-1"></i> Langkah 6: Dokumentasi</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="small text-muted mb-1 font-weight-bold">FOTO KODE PRODUKSI PRIMER</div>
                                        @if($pembekuan->foto_kode_produksi && $pembekuan->foto_kode_produksi !== '-')
                                            <a href="#" data-toggle="modal" data-target="#modalKodeProduksi{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto</a>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalKodeProduksi{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Kode Produksi Primer</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ asset($assetPath . 'storage/' . $pembekuan->foto_kode_produksi) }}" alt="Kode Produksi" style="max-width:100%;max-height:70vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($pembekuan->foto_kode_produksi === '-')
                                            <span>-</span>
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small text-muted mb-1 font-weight-bold">FOTO KODE PRODUKSI SEKUNDER</div>
                                        @php $sekunderVal = $pembekuan->foto_kode_produksi_sekunder; @endphp
                                        @if(in_array($sekunderVal, ['OK', 'Not OK']))
                                            <span class="badge badge-{{ $sekunderVal === 'OK' ? 'success' : 'danger' }} px-2 py-1" style="font-size:0.95rem;">
                                                {{ $sekunderVal }}
                                            </span>
                                        @elseif($sekunderVal && $sekunderVal !== '-')
                                            <a href="#" data-toggle="modal" data-target="#modalKodeProduksiSekunder{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto</a>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalKodeProduksiSekunder{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Kode Produksi Sekunder</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ asset($assetPath . 'storage/' . $sekunderVal) }}" alt="Kode Produksi Sekunder" style="max-width:100%;max-height:70vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($sekunderVal === '-')
                                            <span>-</span>
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small text-muted mb-1 font-weight-bold">QR CODE</div>
                                        @if($pembekuan->qr_code && $pembekuan->qr_code !== '-')
                                            <a href="#" data-toggle="modal" data-target="#modalQrCodeStep6{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto</a>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalQrCodeStep6{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto QR Code</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ asset($assetPath . 'storage/' . $pembekuan->qr_code) }}" alt="QR Code" style="max-width:100%;max-height:70vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($pembekuan->qr_code === '-')
                                            <span>-</span>
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small text-muted mb-1 font-weight-bold">LABEL POLYROLL</div>
                                        @if($pembekuan->label_polyroll && $pembekuan->label_polyroll !== '-')
                                            <a href="#" data-toggle="modal" data-target="#modalPolyroll{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto</a>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalPolyroll{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Label Polyroll</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ asset($assetPath . 'storage/' . $pembekuan->label_polyroll) }}" alt="Label Polyroll" style="max-width:100%;max-height:70vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($pembekuan->label_polyroll === '-')
                                            <span>-</span>
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small text-muted mb-1 font-weight-bold">LABEL SUPPLIER</div>
                                        @if($pembekuan->label_supplier && $pembekuan->label_supplier !== '-')
                                            <a href="#" data-toggle="modal" data-target="#modalLabelSupplier{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto</a>
                                            <div class="modal fade" id="modalLabelSupplier{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Label Supplier</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ asset($assetPath . 'storage/' . $pembekuan->label_supplier) }}" alt="Label Supplier" style="max-width:100%;max-height:70vh;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($pembekuan->label_supplier === '-')
                                            <span>-</span>
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <strong>Keterangan:</strong>
                                        <p>{{ $pembekuan->keterangan ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Verifikasi Hasil Akhir Pengemasan (QC):</strong>
                                        <p>
                                            @php
                                                $verifAkhir = $pembekuan->pengemasanProduk->verifikasi_akhir_pengemasan ?? null;
                                                $statusRel = $pembekuan->pengemasanProduk->status_release_produk ?? null;
                                            @endphp
                                            @if($verifAkhir === '✔' || $statusRel === 'ya')
                                                <span class="badge badge-success px-3 py-2" style="font-size:0.95rem;"><i class="fas fa-check-circle mr-1"></i> ✔ OK (Release)</span>
                                            @elseif($verifAkhir === '✘' || $statusRel === 'tidak')
                                                <span class="badge badge-danger px-3 py-2" style="font-size:0.95rem;"><i class="fas fa-times-circle mr-1"></i> ✘ Tidak OK (Hold / Reject)</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">Menunggu Verifikasi QC</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Tindakan Koreksi:</strong>
                                        <p>{{ $pembekuan->pengemasanProduk->tindakan_koreksi ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== FORM VERIFIKASI QC INSPECTOR (IKON MATA) ===== --}}
                        @if($canEditQcVerification ?? false)
                        <div class="card card-outline card-warning mb-4 shadow" id="qc-verification-section">
                            <div class="card-header bg-warning">
                                <h3 class="card-title text-dark font-weight-bold">
                                    <i class="fas fa-clipboard-check mr-2"></i> Form Verifikasi QC Inspector (E-QF 15)
                                </h3>
                                <div class="card-tools">
                                    @if($isQcCompleted ?? false)
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Data Verifikasi Lengkap</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Belum Diverifikasi</span>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('wizards.packing.qc-verification', $pembekuan->uuid) }}" method="POST" id="form-qc-verification">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    {{-- 1. PEMBEKUAN PRODUK (INPUT ALL) --}}
                                    <div class="card card-secondary card-outline mb-3">
                                        <div class="card-header py-2">
                                            <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-snowflake text-primary mr-1"></i> 1. Pembekuan Produk</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Standar Suhu Produk (Master) <span class="text-danger">*</span></label>
                                                        <input type="text" name="std_suhu_produk_iqf" class="form-control" value="{{ $pembekuan->pengemasanProduk->std_suhu_produk_iqf ?: '-18°C' }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Waktu Pengecekan <span class="text-danger">*</span></label>
                                                        <input type="text" name="waktu_awal_packing" class="form-control" value="{{ $pembekuan->pengemasanProduk->waktu_awal_packing ?? '' }}" placeholder="Contoh: 08:30" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0" id="show-suhu-produk-container">
                                                        <label>Suhu Aktual Produk Pembekuan (°C) <span class="text-danger">*</span></label>
                                                        @php
                                                            $suhuList = array_filter(array_map('trim', explode(',', $pembekuan->pengemasanProduk->aktual_suhu_produk ?? '')), fn($v) => $v !== '');
                                                            if (empty($suhuList)) { $suhuList = ['']; }
                                                        @endphp
                                                        @foreach(array_values($suhuList) as $idx => $sVal)
                                                        <div class="input-group mb-2 show-suhu-group">
                                                            <input type="number" step="0.01" name="aktual_suhu_produk[]" class="form-control" value="{{ $sVal }}" placeholder="Misal: -18.5" required>
                                                            <div class="input-group-append">
                                                                @if($idx === 0)
                                                                    <button type="button" class="btn btn-success add-show-suhu"><i class="fas fa-plus"></i></button>
                                                                @else
                                                                    <button type="button" class="btn btn-danger remove-show-suhu"><i class="fas fa-minus"></i></button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. VERIFIKASI HASIL AKHIR PENGEMASAN --}}
                                    <div class="card card-secondary card-outline mb-0">
                                        <div class="card-header py-2">
                                            <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-check-double text-success mr-1"></i> 2. Verifikasi Hasil Akhir Pengemasan</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $currentVerif = $pembekuan->pengemasanProduk->verifikasi_akhir_pengemasan ?? '';
                                                if ($currentVerif === '' && !empty($pembekuan->pengemasanProduk->status_release_produk)) {
                                                    $currentVerif = $pembekuan->pengemasanProduk->status_release_produk === 'ya' ? '✔' : '✘';
                                                }
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Hasil Akhir Form Pengemasan <span class="text-danger">*</span></label>
                                                        <select name="verifikasi_akhir_pengemasan" class="form-control" required>
                                                            <option value="">-- Pilih Hasil Verifikasi --</option>
                                                            <option value="✔" {{ $currentVerif === '✔' ? 'selected' : '' }}>✔ OK</option>
                                                            <option value="✘" {{ $currentVerif === '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Tindakan Koreksi</label>
                                                        <textarea name="tindakan_koreksi" class="form-control" rows="2" placeholder="Masukkan tindakan koreksi jika ada...">{{ $pembekuan->pengemasanProduk->tindakan_koreksi ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Keterangan</label>
                                                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Tambahkan keterangan jika diperlukan...">{{ $pembekuan->keterangan ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light text-right py-3">
                                    <button type="submit" class="btn btn-success btn-lg font-weight-bold px-4 shadow">
                                        <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    @media print {
        .main-sidebar, .main-header, .card-tools, .btn, .breadcrumb, #qc-verification-section { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .bg-gradient-primary { background: #007bff !important; color: #fff !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>

@push('scripts')
<script>
    // 1. Dynamic Suhu
    $(document).on('click', '.add-show-suhu', function() {
        let container = $('#show-suhu-produk-container');
        let newDiv = `
            <div class="input-group mb-2 show-suhu-group">
                <input type="number" step="0.01" name="aktual_suhu_produk[]" class="form-control" placeholder="Misal: -18.5" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-show-suhu"><i class="fas fa-minus"></i></button>
                </div>
            </div>`;
        container.append(newDiv);
    });
    $(document).on('click', '.remove-show-suhu', function() {
        $(this).closest('.show-suhu-group').remove();
    });
</script>
@endpush
@endsection