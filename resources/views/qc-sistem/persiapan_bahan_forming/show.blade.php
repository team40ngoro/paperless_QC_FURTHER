@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-eye text-primary"></i> Detail Verifikasi Persiapan Cold Mixing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-forming.index') }}">Verifikasi Persiapan Cold Mixing</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <!-- Informasi Umum -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar mr-1"></i>Tanggal</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d H:i:s') : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock mr-1"></i>Shift</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->shift->shift ?? '-' }}" readonly>
                                            <!-- <div class="input-group-append">
                                                <span class="input-group-text">
                                                    @if($data->shift_id == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($data->shift_id == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @else
                                                        <span class="badge bg-secondary">Shift {{ $data->shift_id }}</span>
                                                    @endif
                                                </span>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam</label>
                                        <input type="text" class="form-control" value="{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-box mr-1"></i>Nama Produk</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->formula->produk->nama_produk ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-cube"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-barcode mr-1"></i>Nomor Formula</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->formula->nomor_formula ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-weight mr-1"></i>Gramase (gram)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->berat_sampling ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-code mr-1"></i>Kode Produksi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->kode_produksi_emulsi }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-code mr-1"></i>Kode Produksi Emulsi Oil</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ is_array($data->kode_produksi_emulsi_oil) ? implode(', ', $data->kode_produksi_emulsi_oil) : $data->kode_produksi_emulsi_oil }}" readonly>                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Suhu Adonan -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-thermometer-half mr-2"></i>Suhu Adonan</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-thermometer-half mr-1"></i>Suhu Adonan (STD)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->suhuAdonan->std_suhu ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Suhu Forming -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-fire mr-2"></i>Suhu Forming</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Bahan Forming</th>
                                            <th class="text-center">Berat RM</th>
                                            <th class="text-center">Kode Produksi Bahan</th>
                                            <th class="text-center">Suhu RM (°C)</th>
                                            <th class="text-center">Sensori</th>
                                            <th class="text-center">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data->suhuForming as $index => $suhu)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $suhu->bahanForming->nama_rm ?? '-' }}</td>
                                            <td class="text-center">{{ $suhu->bahanForming->berat_rm ?? '-' }}</td>
                                            <td class="text-center">{{ $suhu->kode_produksi_bahan }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $suhu->suhu }}°C</span>
                                            </td>
                                            <td class="text-center">{{ $suhu->sensori ?? '-' }}</td>
                                            <td class="text-center">{{ $suhu->keterangan ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada data suhu forming</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($data->suhuForming->count() > 0)
                                    @php
                                        $getFloat = function($val) {
                                            if (is_null($val) || $val === '' || is_array($val)) return 0.0;
                                            $clean = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string)$val));
                                            return is_numeric($clean) ? (float)$clean : 0.0;
                                        };

                                        $beratList = $data->suhuForming->map(fn($s) => $getFloat($s->bahanForming->berat_rm ?? 0));

                                        $totalBerat = $beratList->sum();
                                        $avgBerat   = $beratList->count() > 0 ? $beratList->avg() : 0;
                                    @endphp
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="2" class="text-right">Total Berat RM:</td>
                                            <td class="text-center text-primary">{{ number_format($totalBerat, 3) }}</td>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right">Rata-Rata Berat RM:</td>
                                            <td class="text-center text-success">{{ number_format($avgBerat, 3) }}</td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>

                            <!-- Suhu Aktual -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-success"><i class="fas fa-temperature-high mr-2"></i>Suhu Aktual</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 1</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_1 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 2</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_2 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 3</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_3 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 4</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_4 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 5</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_5 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Total (Rata-rata)</label>
                                        <div class="input-group">
                                            @php($avg = optional($data->aktualSuhuAdonan)->total_aktual_suhu)
                                            <input type="text" class="form-control bg-success text-white" value="{{ $avg !== null ? number_format($avg, 1) : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Waktu dan Informasi Lainnya -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-clock mr-2"></i>Waktu dan Informasi Lainnya</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-play mr-1"></i>Waktu Mulai Mixing</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->waktu_mulai_mixing ?: '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-stop mr-1"></i>Waktu Selesai Mixing</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->waktu_selesai_mixing ?: '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-boxes mr-1"></i>Produk Rework</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $produkReworkNama }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-recycle mr-1"></i>Rework</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->rework }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-info-circle mr-1"></i>Kondisi Hasil Penggilingan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->kondisi }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    @if(strpos($data->kondisi, '✔') !== false)
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @else
                                                        <i class="fas fa-exclamation-circle text-warning"></i>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-info-circle mr-1"></i>Kondisi Hasil Pencampuran</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->kondisi_hasil_pencampuran }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    @if(strpos($data->kondisi_hasil_pencampuran, '✔') !== false)
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @else
                                                        <i class="fas fa-exclamation-circle text-warning"></i>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-sticky-note mr-1"></i>Catatan</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $data->catatan ?? '-' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-forming'))
                                    <a href="{{ route('persiapan-bahan-forming.edit', $data->uuid) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Data
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-persiapan-bahan-forming'))
                                    <a href="{{ route('persiapan-bahan-forming.logs', $data->uuid) }}" class="btn btn-info">
                                        <i class="fas fa-history"></i> Lihat Riwayat
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
