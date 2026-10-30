@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Produk Non Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk-non-forming.index') }}">Produk Non Forming</a></li>
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
                            <h3 class="card-title">Detail Data Produk Non Forming</h3>
                            <div class="card-tools">
                                
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Informasi Dasar</h5>
                                        </div>
                                        
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Tanggal:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($produkNonForming->tanggal)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Produk:</strong></td>
                                                    <td>{{ $produkNonForming->produk->nama_produk ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Plan:</strong></td>
                                                    <td>{{ $produkNonForming->plan->nama_plan ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Shift:</strong></td>
                                                    <td>{{ $produkNonForming->shift->shift ?? '-' }}</td>
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
                                                                <ol class="mb-0" style="font-size: 0.9em;">
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

                            <!-- Bahan Baku dan Bahan Penunjang -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Bahan Baku & Bahan Penunjang</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $allBahan = array_merge(
                                                    $produkNonForming->bahan_baku ?? [],
                                                    $produkNonForming->bahan_penunjang ?? []
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
                                                <p class="text-muted">Tidak ada data bahan baku dan bahan penunjang</p>
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
                                                                $kVal = $produkNonForming->{$kItem['field']} ?? [];
                                                                if (!is_array($kVal)) $kVal = $kVal ? [$kVal] : [];
                                                                $kTindakan = $produkNonForming->{$kItem['field'].'_tindakan'} ?? '-';
                                                                $kVerif = $produkNonForming->{$kItem['field'].'_verifikasi'} ?? '';
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
                            <!-- C. Mesin Dan Peralatan -->
                            @php
                                $hasMesinData = false;
                                if (!empty($produkNonForming->mesin_peralatan) && is_array($produkNonForming->mesin_peralatan)) {
                                    foreach ($produkNonForming->mesin_peralatan as $m) {
                                        // Item yang ditandai "dihapus" saat pengisian form tidak dihitung sebagai data
                                        if (($m['dihapus'] ?? '0') == '1') continue;
                                        $k = $m['kondisi'] ?? [];
                                        if (is_array($k) ? count(array_filter($k)) > 0 : !empty($k)) {
                                            $hasMesinData = true; break;
                                        }
                                    }
                                }
                                if (!$hasMesinData && !empty($produkNonForming->penilaian) && is_array($produkNonForming->penilaian)) {
                                    foreach ($produkNonForming->penilaian as $m) {
                                        if (!empty($m['nilai'] ?? '')) { $hasMesinData = true; break; }
                                    }
                                }
                                if (!$hasMesinData) {
                                    $legacyFields = ['tumbler','frayer','hicook','iqf_advance_1','iqf_advance_2','keranjang','palet','meatcar','timbangan','mhw','foot_sealer','metal_detector','check_weigher_bag','check_weigher_box','karton_sealer'];
                                    foreach ($legacyFields as $f) {
                                        if (!empty($produkNonForming->$f)) { $hasMesinData = true; break; }
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

                                                if (!empty($produkNonForming->mesin_peralatan) && is_array($produkNonForming->mesin_peralatan)) {
                                                    $mesinData = $produkNonForming->mesin_peralatan;
                                                    $isNewFormat = true;
                                                } elseif (!empty($produkNonForming->penilaian) && is_array($produkNonForming->penilaian)) {
                                                    $mesinData = $produkNonForming->penilaian;
                                                    $isNewFormat = false;
                                                }

                                                // Sembunyikan item yang ditandai "dihapus" (tidak dipakai) saat pengisian form
                                                $mesinDataToShow = collect($mesinData)->reject(function ($m) {
                                                    return ($m['dihapus'] ?? '0') == '1';
                                                })->values();
                                            @endphp

                                            @if(count($mesinData) > 0)
                                                @if($mesinDataToShow->count() > 0)
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
                                                                @foreach($mesinDataToShow as $index => $mesin)
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
                                                    <p class="text-muted">Semua item mesin/peralatan ditandai "tidak dipakai" pada form ini.</p>
                                                @endif
                                            @else
                                                @php
                                                    $mesinFields = [
                                                        'tumbler' => 'Tumbler',
                                                        'frayer' => 'Frayer',
                                                        'hicook' => 'Hicook',
                                                        'iqf_advance_1' => 'IQF Advance 1',
                                                        'iqf_advance_2' => 'IQF Advance 2',
                                                        'keranjang' => 'Keranjang',
                                                        'palet' => 'Palet',
                                                        'meatcar' => 'Meatcar',
                                                        'timbangan' => 'Timbangan',
                                                        'mhw' => 'MHW',
                                                        'foot_sealer' => 'Foot Sealer',
                                                        'metal_detector' => 'Metal Detector',
                                                        'check_weigher_bag' => 'Check Weigher Bag',
                                                        'check_weigher_box' => 'Check Weigher Box',
                                                        'karton_sealer' => 'Karton Sealer'
                                                    ];

                                                    $filledMesinFields = array_filter($mesinFields, function ($label, $field) use ($produkNonForming) {
                                                        $value = $produkNonForming->$field ?? null;
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
                                                                        <span class="badge badge-{{ $produkNonForming->$field == '✓' ? 'success' : ($produkNonForming->$field == '✗' ? 'danger' : 'warning') }} float-right">
                                                                            {{ $produkNonForming->$field }}
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
                            @php
                                $ruanganDataToShow = collect($produkNonForming->ruangan ?? [])->reject(function ($r) {
                                    return ($r['dihapus'] ?? '0') == '1';
                                })->values();
                            @endphp
                            @if($ruanganDataToShow->count() > 0)
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
                                                        @foreach($ruanganDataToShow as $idx => $ruangan)
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
                                            <p class="mb-0">{{ $produkNonForming->keterangan ?: 'Tidak ada keterangan/catatan.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || $produkNonForming->user_id == Auth::id())
@if(auth()->user()->hasPermissionTo('edit-produk-non-forming'))
                                <a href="{{ route('produk-non-forming.edit', $produkNonForming->uuid) }}" class="btn btn-md btn-warning">
                                    <i class="fas fa-edit"></i> Edit Data
                                </a>
                           @endif
 @endif
                            <a href="{{ route('produk-non-forming.index') }}" class="btn btn-secondary btn-md">
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