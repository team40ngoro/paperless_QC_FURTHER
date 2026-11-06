@extends('layouts.app')

@section('container')
@php
    $currentUser = auth()->user();
    $isSuperadmin = $currentUser->role === 'superadmin' || (int) ($currentUser->id_role ?? 0) === 5;
    $isQcInspector = !$isSuperadmin && ($currentUser->isQcInspector() || (int) ($currentUser->id_role ?? 0) === 3);
    $canEditTemuan = $isSuperadmin || $currentUser->isQc();
    $canEditOthers = $isSuperadmin || !$isQcInspector;
@endphp
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('barang-mudah-pecah.index') }}">Barang Mudah Pecah</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Barang Mudah Pecah</h3>
                        </div>
                        
                        <form action="{{ route('barang-mudah-pecah.update', $barangMudahPecah->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    {{-- update jam dan tanggal --}}
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Tanggal</label>

                                            <input type="date" class="form-control"
                                                value="{{ $barangMudahPecah->tanggal ? \Carbon\Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d') : '' }}"
                                                @if($canEditOthers)
                                                    onchange="this.form.querySelector('input[name=tanggal]').value = this.value + 'T' + '{{ $barangMudahPecah->tanggal ? \Carbon\Carbon::parse($barangMudahPecah->tanggal)->format('H:i:s') : '' }}'"
                                                @else
                                                    readonly style="background:#f8f9fa;cursor:not-allowed;"
                                                @endif>

                                            <input type="hidden" name="tanggal"
                                                value="{{ $barangMudahPecah->tanggal ? \Carbon\Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d\TH:i:s') : '' }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jam</label>
                                            <input type="time" class="form-control" name="jam"
                                                value="{{ $barangMudahPecah->jam ? \Carbon\Carbon::parse($barangMudahPecah->jam)->format('H:i') : '' }}"
                                                @unless($canEditOthers) readonly style="background:#f8f9fa;cursor:not-allowed;" @endunless>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Shift</label>
                                            <input type="text" class="form-control" value="{{ $barangMudahPecah->shift->shift ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
<!--  -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="barang-mudah-pecah-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60px;">No</th>
                                                        <th>Nama Barang</th>
                                                        <th style="width: 120px;">Jumlah</th>
                                                        <th style="width: 170px;">Kondisi</th>
                                                        <th>Temuan Ketidaksesuaian</th>
                                                        <th>Tindakan Koreksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                        $rowIndex = 0;
                                                        
                                                        // Flatten items for display
                                                        $flattenedItems = collect();
                                                        foreach($groupItems as $record) {
                                                            if ($record->items_json) {
                                                                foreach($record->items_json as $jsonIdx => $jsonRow) {
                                                                    $flattenedItems->push((object)[
                                                                        'is_json' => true,
                                                                        'record_uuid' => $record->uuid,
                                                                        'json_index' => $jsonIdx,
                                                                        'area_id' => $jsonRow['id_area'],
                                                                        'area_name' => \App\Models\InputArea::find($jsonRow['id_area'])->area ?? 'Area',
                                                                        'nama_barang' => $jsonRow['nama_barang'] ?? '-',
                                                                        'jumlah' => $jsonRow['jumlah'] ?? 0,
                                                                        'kondisi' => $jsonRow['kondisi'] ?? '',
                                                                        'temuan_ketidaksesuaian' => $jsonRow['temuan_ketidaksesuaian'] ?? '',
                                                                        'tindakan_koreksi' => $jsonRow['tindakan_koreksi'] ?? '',
                                                                        'is_manual' => false
                                                                    ]);
                                                                }
                                                            } else {
                                                                $flattenedItems->push((object)[
                                                                    'is_json' => false,
                                                                    'record_uuid' => $record->uuid,
                                                                    'area_id' => $record->id_area,
                                                                    'area_name' => $record->area->area ?? 'Area',
                                                                    'nama_barang' => $record->is_manual ? ($record->nama_barang_manual ?? '-') : ($record->namaBarang->nama_barang ?? '-'),
                                                                    'jumlah' => $record->jumlah ?? 0,
                                                                    'kondisi' => $record->kondisi ?? '',
                                                                    'temuan_ketidaksesuaian' => $record->temuan_ketidaksesuaian ?? '',
                                                                    'tindakan_koreksi' => $record->tindakan_koreksi ?? '',
                                                                    'is_manual' => $record->is_manual,
                                                                    'nama_karyawan' => $record->is_manual ? $record->nama_karyawan : null
                                                                ]);
                                                            }
                                                        }
                                                        $grouped = $flattenedItems->groupBy('area_id');
                                                    @endphp

                                                    @forelse($grouped as $areaId => $items)
                                                        @php $areaName = $items->first()->area_name; @endphp
                                                        <tr class="table-secondary">
                                                            <td colspan="6" class="font-weight-bold">
                                                                {{ $areaName }}
                                                            </td>
                                                        </tr>

                                                        @foreach($items as $item)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>
                                                                    {{ $item->nama_barang }}
                                                                    @if($item->is_manual)
                                                                        <div class="mt-2">
                                                                            <label class="small text-muted mb-0">Nama Karyawan:</label>
                                                                            <input type="text" 
                                                                                   class="form-control form-control-sm" 
                                                                                   name="items[{{ $rowIndex }}][nama_karyawan]" 
                                                                                   value="{{ old("items.$rowIndex.nama_karyawan", $item->nama_karyawan) }}" 
                                                                                   placeholder="Nama Karyawan"
                                                                                   @unless($canEditOthers) readonly style="background:#f8f9fa;cursor:not-allowed;" @endunless>
                                                                        </div>
                                                                    @endif
                                                                    <input type="hidden" name="items[{{ $rowIndex }}][record_uuid]" value="{{ $item->record_uuid }}">
                                                                    @if($item->is_json)
                                                                        <input type="hidden" name="items[{{ $rowIndex }}][json_index]" value="{{ $item->json_index }}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control" value="{{ $item->jumlah }}" readonly>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" name="items[{{ $rowIndex }}][kondisi]" required
                                                                            @unless($canEditOthers) style="pointer-events: none; background-color: #f8f9fa;" tabindex="-1" @endunless>
                                                                        <option value="OK" {{ old("items.$rowIndex.kondisi", $item->kondisi) == 'OK' ? 'selected' : '' }}>OK</option>
                                                                        <option value="Tidak OK" {{ old("items.$rowIndex.kondisi", $item->kondisi) == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <textarea class="form-control" name="items[{{ $rowIndex }}][temuan_ketidaksesuaian]" rows="1" placeholder="{{ $canEditTemuan ? 'Masukkan temuan (opsional)' : 'Hanya QC yang dapat mengisi' }}" @unless($canEditTemuan) readonly style="background:#f8f9fa;cursor:not-allowed;" @endunless>{{ old("items.$rowIndex.temuan_ketidaksesuaian", $item->temuan_ketidaksesuaian) }}</textarea>
                                                                </td>
                                                                <td>
                                                                    <textarea class="form-control" name="items[{{ $rowIndex }}][tindakan_koreksi]" rows="1" placeholder="{{ $canEditOthers ? 'Masukkan tindakan (opsional)' : 'Hanya Produksi yang dapat mengisi' }}" @unless($canEditOthers) readonly style="background:#f8f9fa;cursor:not-allowed;" @endunless>{{ old("items.$rowIndex.tindakan_koreksi", $item->tindakan_koreksi) }}</textarea>
                                                                </td>
                                                            </tr>
                                                            @php $rowIndex++; @endphp
                                                        @endforeach
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">Data tidak ditemukan.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('barang-mudah-pecah.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    });
</script>
@endpush