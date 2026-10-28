@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('barang-mudah-pecah.index') }}">Barang Mudah Pecah</a></li>
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
                                    Detail Barang Mudah Pecah
                                </h3>
                                <a href="{{ route('barang-mudah-pecah.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $userRole = auth()->user()->id_role ?? null;
                                $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                            @endphp

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->tanggal ? \Carbon\Carbon::parse($barangMudahPecah->tanggal)->format($format) : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Shift</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->shift->shift ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Waktu</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->jam ? \Carbon\Carbon::parse($barangMudahPecah->jam)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Area</label>
                                        <div class="form-control bg-light">
                                            @php
                                                $areas = $groupItems->map(function($item) {
                                                    if ($item->items_json) {
                                                        $areaIds = collect($item->items_json)->pluck('id_area')->unique();
                                                        return \App\Models\InputArea::whereIn('id', $areaIds)->pluck('area')->toArray();
                                                    }
                                                    return [$item->area->area ?? '-'];
                                                })->flatten()->unique()->filter()->toArray();
                                            @endphp
                                            {{ implode(', ', $areas) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 60px;">No</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center" style="width: 120px;">Jumlah</th>
                                            <th class="text-center" style="width: 140px;">Kondisi</th>
                                            <th>Temuan Ketidaksesuaian</th>
                                            <th>Tindakan Koreksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php 
                                            $no = 1;
                                            $flattenedForShow = collect();
                                            foreach($groupItems as $record) {
                                                if ($record->items_json) {
                                                    foreach($record->items_json as $jsonRow) {
                                                        $flattenedForShow->push((object)[
                                                            'area_name' => \App\Models\InputArea::find($jsonRow['id_area'])->area ?? 'Area',
                                                            'id_area' => $jsonRow['id_area'],
                                                            'nama_barang' => $jsonRow['nama_barang'] ?? '-',
                                                            'jumlah' => $jsonRow['jumlah'] ?? '-',
                                                            'kondisi' => $jsonRow['kondisi'] ?? '-',
                                                            'temuan' => $jsonRow['temuan_ketidaksesuaian'] ?? '',
                                                            'tindakan' => $jsonRow['tindakan_koreksi'] ?? '',
                                                            'nama_karyawan' => null
                                                        ]);
                                                    }
                                                } else {
                                                    $flattenedForShow->push((object)[
                                                        'area_name' => $record->area->area ?? 'Area',
                                                        'id_area' => $record->id_area,
                                                        'nama_barang' => $record->is_manual ? ($record->nama_barang_manual ?? '-') : ($record->namaBarang->nama_barang ?? '-'),
                                                        'jumlah' => $record->jumlah ?? '-',
                                                        'kondisi' => $record->kondisi ?? '-',
                                                        'temuan' => $record->temuan_ketidaksesuaian ?? '',
                                                        'tindakan' => $record->tindakan_koreksi ?? '',
                                                        'nama_karyawan' => $record->is_manual ? $record->nama_karyawan : null
                                                    ]);
                                                }
                                            }
                                            $groupedForShow = $flattenedForShow->groupBy('id_area');
                                        @endphp
<!--  -->
                                        @foreach($groupedForShow as $areaId => $items)
                                            <tr class="table-secondary">
                                                <td colspan="6" class="font-weight-bold">{{ $items->first()->area_name }}</td>
                                            </tr>
                                            @foreach($items as $item)
                                                <tr>
                                                    <td class="text-center">{{ $no++ }}</td>
                                                    <td>
                                                        {{ $item->nama_barang }}
                                                        @if($item->nama_karyawan)
                                                            <div class="small text-muted">
                                                                <i class="fas fa-user-edit mr-1"></i> {{ $item->nama_karyawan }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $item->jumlah }}</td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $item->kondisi === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            <i class="{{ $item->kondisi === 'OK' ? 'fas fa-check' : 'fas fa-times' }}"></i>
                                                            {{ $item->kondisi }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($item->temuan)
                                                            {{ $item->temuan }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->tindakan)
                                                            {{ $item->tindakan }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
