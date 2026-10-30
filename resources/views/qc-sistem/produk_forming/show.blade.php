@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Produk Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk-forming.index') }}">Produk Forming</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Data Produk Forming</h3>
                            <div class="card-tools">
                                
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Informasi Dasar</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Tanggal:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($produkForming->tanggal)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Produk:</strong></td>
                                                    <td>{{ $produkForming->produk->nama_produk ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Plan:</strong></td>
                                                    <td>{{ $produkForming->plan->nama_plan ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Shift:</strong></td>
                                                    <td>{{ $produkForming->shift->shift ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Informasi Keterangan -->
                            <div class="row mt-3">
                                <div class="col-12">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i> Keterangan Pengecekan & Kriteria Penilaian
                                                </h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5 class="text-primary"><i class="fas fa-clipboard-check"></i> Keterangan Pengecekan:</h5>
                                                        <ul class="list-unstyled">
                                                           <li class="mb-2">
                                                <i class="fas fa-dot-circle text-info"></i>
                                                <strong> Pengecekan Kondisi Sisa Bahan/Kemasan (1 - 5):</strong> nomer 1-8
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-dot-circle text-info"></i>
                                                <strong>Pengecekan Kondisi Mesin dan Peralatan :</strong> nomer 3-8
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-dot-circle text-info"></i>
                                                <strong>Pengecekan Kondisi Ruangan:</strong> nomer 3-8
                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5 class="text-success"><i class="fas fa-star"></i> Kriteria Penilaian:</h5>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <ol class="mb-0" style="font-size: 1.0em;">
                                                                        <li>Sesuai spesifikasi</li>
                                                                        <li>Tidak sesuai spesifikasi</li>
                                                                        <li>Bebas dari kontaminan dan bahan sebelumnya</li>
                                                                        <li>Ada kontaminan atau sisa bahan sebelumnya</li>
                                                                        <li>Bebas dari potensi kontaminasi allergen</li>
                                                                        <li>Ada potensi kontaminasi allergen</li>
                                                                        <li>Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang</li>
                                                                        <li>Tidak bersih, ada kontaminan atau kotoran, tercium bau menyimpang</li>
                                                                    </ol>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>Petunjuk:</strong> Gunakan nomor kriteria penilaian di atas untuk mengisi form penilaian pada setiap aspek pemeriksaan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bahan Baku -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Bahan Baku & Bahan Penunjang</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $allBahan = array_merge(
                                                    $produkForming->bahan_baku ?? [],
                                                    $produkForming->bahan_penunjang ?? []
                                                );
                                            @endphp

                                            @if(count($allBahan) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th width="50">No</th>
                                                                <th>Nama Bahan</th>
                                                                <th width="150">Penilaian</th>
                                                                <th>Tindakan Koreksi</th>
                                                                <th width="100">Verifikasi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($allBahan as $index => $bahan)
                                                                @php
                                                                    $penilaianArr = is_array($bahan['penilaian'] ?? null) ? $bahan['penilaian'] : (isset($bahan['penilaian']) ? [$bahan['penilaian']] : []);
                                                                    $isSisaKemasan = strtolower(trim($bahan['nama'] ?? '')) === 'sisa kemasan';
                                                                    // Sembunyikan Sisa Kemasan jika tidak ada penilaian
                                                                    if ($isSisaKemasan && empty(array_filter($penilaianArr))) continue;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $bahan['nama'] ?? '-' }}</td>
                                                                    <td>{{ count($penilaianArr) ? implode(', ', $penilaianArr) : '-' }}</td>
                                                                    <td>{{ $bahan['tindakan_koreksi'] ?? '-' }}</td>
                                                                    <td>
                                                                        @if(($bahan['verifikasi'] ?? '') == '✓')
                                                                            <span class="badge badge-success">Ok (✓)</span>
                                                                        @elseif(($bahan['verifikasi'] ?? '') == '✗')
                                                                            <span class="badge badge-danger">Tidak Ok (✗)</span>
                                                                        @else
                                                                            <span class="badge badge-warning">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Tidak ada data bahan baku & bahan penunjang</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kemasan Information -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Kemasan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="50">No</th>
                                                            <th>Nama Item</th>
                                                            <th>Penilaian</th>
                                                            <th>Tindakan Koreksi</th>
                                                            <th width="100">Verifikasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $kemasanShowItems = [
                                                                ['field' => 'kemasan_plastik',    'label' => 'Kemasan Plastik'],
                                                                ['field' => 'kemasan_karton',     'label' => 'Kemasan Karton'],
                                                                ['field' => 'labelisasi_plastik', 'label' => 'Labelisasi Plastik'],
                                                                ['field' => 'labelisasi_karton',  'label' => 'Labelisasi Karton'],
                                                            ];
                                                        @endphp
                                                        @foreach($kemasanShowItems as $ki => $kItem)
                                                            @php
                                                                $kVal = $produkForming->{$kItem['field']} ?? [];
                                                                if (!is_array($kVal)) $kVal = $kVal ? [$kVal] : [];
                                                                $kTindakan = $produkForming->{$kItem['field'].'_tindakan'} ?? '-';
                                                                $kVerif = $produkForming->{$kItem['field'].'_verifikasi'} ?? '';
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $ki + 1 }}</td>
                                                                <td>{{ $kItem['label'] }}</td>
                                                                <td>{{ count($kVal) ? implode(', ', $kVal) : '-' }}</td>
                                                                <td>{{ $kTindakan ?: '-' }}</td>
                                                                <td>
                                                                    @if($kVerif == '✓')
                                                                        <span class="badge badge-success">Ok (✓)</span>
                                                                    @elseif($kVerif == '✗')
                                                                        <span class="badge badge-danger">Tidak Ok (✗)</span>
                                                                    @else
                                                                        <span class="badge badge-warning">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mesin Dan Peralatan -->
                            @php
                                $hasMesinData = false;
                                if (!empty($produkForming->mesin_peralatan) && is_array($produkForming->mesin_peralatan)) {
                                    foreach ($produkForming->mesin_peralatan as $m) {
                                        $k = $m['kondisi'] ?? [];
                                        if (is_array($k) ? count(array_filter($k)) > 0 : !empty($k)) {
                                            $hasMesinData = true; break;
                                        }
                                    }
                                }
                                if (!$hasMesinData && !empty($produkForming->penilaian) && is_array($produkForming->penilaian)) {
                                    foreach ($produkForming->penilaian as $m) {
                                        if (!empty($m['nilai'] ?? '')) { $hasMesinData = true; break; }
                                    }
                                }
                            @endphp
                            @if($hasMesinData)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-cogs mr-2"></i>Mesin Dan Peralatan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $mesinData = [];
                                                $isNewFormat = false;

                                                if (!empty($produkForming->mesin_peralatan) && is_array($produkForming->mesin_peralatan)) {
                                                    $mesinData = $produkForming->mesin_peralatan;
                                                    $isNewFormat = true;
                                                } elseif (!empty($produkForming->penilaian) && is_array($produkForming->penilaian)) {
                                                    $mesinData = $produkForming->penilaian;
                                                    $isNewFormat = false;
                                                }
                                            @endphp

                                            @if(count($mesinData) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th width="50">No</th>
                                                                <th>Nama Mesin</th>
                                                                <th>Kondisi</th>
                                                                <th>Tindakan Koreksi</th>
                                                                <th width="100">Verifikasi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($mesinData as $index => $mesin)
                                                                @php
                                                                    $namaMesin = $isNewFormat ? ($mesin['nama_mesin'] ?? '-') : ($mesin['aspek'] ?? '-');
                                                                    $kondisi = $isNewFormat ? ($mesin['kondisi'] ?? '-') : ($mesin['nilai'] ?? '-');
                                                                    $kondisiStr = is_array($kondisi) ? implode(', ', $kondisi) : $kondisi;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $namaMesin }}</td>
                                                                    <td>{{ $kondisiStr }}</td>
                                                                    <td>{{ $mesin['tindakan_koreksi'] ?? '-' }}</td>
                                                                    <td>
                                                                        @if(($mesin['verifikasi'] ?? '') == '✓')
                                                                            <span class="badge badge-success">Ok (✓)</span>
                                                                        @elseif(($mesin['verifikasi'] ?? '') == '✗')
                                                                            <span class="badge badge-danger">Tidak Ok (✗)</span>
                                                                        @else
                                                                            <span class="badge badge-warning">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                @php
                                                    $mesinFields = [
                                                        'autogrind' => 'Autogrind',
                                                        'bowlcutter' => 'Bowlcutter',
                                                        'ayakan_seasoning' => 'Ayakan Seasoning',
                                                        'unimix' => 'Unimix',
                                                        'revoformer' => 'Revoformer',
                                                        'better_mixer' => 'Better Mixer',
                                                        'wet_coater' => 'Wet Coater',
                                                        'breader' => 'Breader',
                                                        'frayer_1' => 'Frayer 1',
                                                        'frayer_2' => 'Frayer 2',
                                                        'iqf_jbt' => 'IQF JBT',
                                                        'keranjang' => 'Keranjang',
                                                        'timbangan' => 'Timbangan',
                                                        'mhw' => 'MHW',
                                                        'foot_sealer' => 'Foot Sealer',
                                                        'metal_detector' => 'Metal Detector',
                                                        'rotary_table' => 'Rotary Table',
                                                        'carton_sealer' => 'Carton Sealer',
                                                        'meatcar' => 'Meatcar',
                                                        'check_weigher_bag' => 'Check Weigher Bag',
                                                        'check_weigher_box' => 'Check Weigher Box'
                                                    ];

                                                    $filledMesinFields = array_filter($mesinFields, function ($label, $field) use ($produkForming) {
                                                        $value = $produkForming->$field ?? null;
                                                        return $value !== null && $value !== '';
                                                    }, ARRAY_FILTER_USE_BOTH);
                                                @endphp

                                                @if(count($filledMesinFields) > 0)
                                                    <div class="row">
                                                        @foreach($filledMesinFields as $field => $label)
                                                            <div class="col-md-4 mb-3">
                                                                <div class="card card-outline card-info">
                                                                    <div class="card-body p-2">
                                                                        <strong>{{ $label }}:</strong>
                                                                        <span class="badge badge-{{ $produkForming->$field == '✓' ? 'success' : ($produkForming->$field == '✗' ? 'danger' : 'warning') }} float-right">
                                                                            {{ $produkForming->$field }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-muted">Tidak ada data mesin dan peralatan</p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- D. Ruangan -->
                            @if(!empty($produkForming->ruangan) && count($produkForming->ruangan) > 0)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-door-open mr-2"></i>Ruangan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="50">No</th>
                                                            <th>Nama Ruangan</th>
                                                            <th>Kondisi</th>
                                                            <th>Tindakan Koreksi</th>
                                                            <th width="100">Verifikasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($produkForming->ruangan as $idx => $ruangan)
                                                            @php
                                                                $kondisiArr = is_array($ruangan['kondisi'] ?? null) ? $ruangan['kondisi'] : (isset($ruangan['kondisi']) ? [$ruangan['kondisi']] : []);
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $ruangan['nama_ruangan'] ?? '-' }}</td>
                                                                <td>{{ count($kondisiArr) ? implode(', ', $kondisiArr) : '-' }}</td>
                                                                <td>{{ $ruangan['tindakan_koreksi'] ?? '-' }}</td>
                                                                <td>
                                                                    @if(($ruangan['verifikasi'] ?? '') == '✓')
                                                                        <span class="badge badge-success">Ok (✓)</span>
                                                                    @elseif(($ruangan['verifikasi'] ?? '') == '✗')
                                                                        <span class="badge badge-danger">Tidak Ok (✗)</span>
                                                                    @else
                                                                        <span class="badge badge-warning">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Keterangan / Catatan -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-sticky-note mr-2"></i>Keterangan / Catatan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $produkForming->keterangan ?: 'Tidak ada keterangan/catatan.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || $produkForming->user_id == Auth::id())
                                @if(auth()->user()->hasPermissionTo('edit-produk-forming'))
                                <a href="{{ route('produk-forming.edit', $produkForming->uuid) }}" class="btn btn-md btn-warning">
                                    <i class="fas fa-edit"></i> Edit Data
                                </a>
                                @endif
                            @endif
                            <a href="{{ route('produk-forming.index') }}" class="btn btn-secondary btn-md">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.875em;
}
</style>
@endpush
