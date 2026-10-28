@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Pemeriksaan Bahan Kemas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-bahan-kemas.index') }}">Pemeriksaan Bahan Kemas</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-plus text-primary mr-2"></i>
                                Form Tambah Pemeriksaan Bahan Kemas
                            </h3>
                        </div>
                        <form action="{{ route('pemeriksaan-bahan-kemas.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal" id="tanggal" 
                                                   value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $oldItems = old('items');
                                    if (!is_array($oldItems) || count($oldItems) < 1) {
                                        $oldItems = [[
                                            'jam' => \Carbon\Carbon::now('Asia/Jakarta')->format('H:i'),
                                            'nama_kemasan' => '',
                                            'kode_produksi' => '',
                                            'kondisi_bahan_kemasan' => '',
                                            'keterangan' => '',
                                        ]];
                                    }
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="font-weight-bold mb-0">Detail Pemeriksaan <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                                        <i class="fas fa-plus"></i> Tambah Baris
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:60px;">No</th>
                                                <th class="text-center">Jam</th>
                                                <th class="text-center">Nama Kemasan</th>
                                                <th class="text-center">Kode Produksi</th>
                                                <th class="text-center">Kondisi</th>
                                                <th class="text-center">Keterangan</th>
                                                <th style="width:90px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($oldItems as $idx => $row)
                                                <tr class="item-row">
                                                    <td class="text-center align-middle row-no">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <input type="time" name="items[{{ $idx }}][jam]" class="form-control @error('items.' . $idx . '.jam') is-invalid @enderror" value="{{ $row['jam'] ?? '' }}" required>
                                                        @error('items.' . $idx . '.jam')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $idx }}][nama_kemasan]" class="form-control @error('items.' . $idx . '.nama_kemasan') is-invalid @enderror" value="{{ $row['nama_kemasan'] ?? '' }}" required>
                                                        @error('items.' . $idx . '.nama_kemasan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $idx }}][kode_produksi]" class="form-control @error('items.' . $idx . '.kode_produksi') is-invalid @enderror" value="{{ $row['kode_produksi'] ?? '' }}" required>
                                                        @error('items.' . $idx . '.kode_produksi')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select name="items[{{ $idx }}][kondisi_bahan_kemasan]" class="form-control @error('items.' . $idx . '.kondisi_bahan_kemasan') is-invalid @enderror" required>
                                                            <option value="">Pilih</option>
                                                            <option value="OK" {{ ($row['kondisi_bahan_kemasan'] ?? '') === 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="Tidak OK" {{ ($row['kondisi_bahan_kemasan'] ?? '') === 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                        </select>
                                                        @error('items.' . $idx . '.kondisi_bahan_kemasan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <textarea name="items[{{ $idx }}][keterangan]" class="form-control @error('items.' . $idx . '.keterangan') is-invalid @enderror" rows="2">{{ $row['keterangan'] ?? '' }}</textarea>
                                                        @error('items.' . $idx . '.keterangan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-danger btnRemoveRow">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('pemeriksaan-bahan-kemas.index') }}" class="btn btn-secondary">
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
(function() {
    function renumberRows() {
        const rows = document.querySelectorAll('#itemsTable tbody tr.item-row');
        rows.forEach((row, idx) => {
            const noEl = row.querySelector('.row-no');
            if (noEl) noEl.textContent = idx + 1;

            row.querySelectorAll('input[name^="items["] , select[name^="items["]').forEach((el) => {
                const name = el.getAttribute('name');
                if (!name) return;
                el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + idx + ']'));
            });
        });
    }

    document.getElementById('btnAddRow')?.addEventListener('click', function() {
        const tbody = document.querySelector('#itemsTable tbody');
        const rowCount = document.querySelectorAll('#itemsTable tbody tr.item-row').length;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center align-middle row-no">${rowCount + 1}</td>
            <td>
                <input type="time" name="items[${rowCount}][jam]" class="form-control" required>
            </td>
            <td>
                <input type="text" name="items[${rowCount}][nama_kemasan]" class="form-control" required>
            </td>
            <td>
                <input type="text" name="items[${rowCount}][kode_produksi]" class="form-control" required>
            </td>
            <td>
                <select name="items[${rowCount}][kondisi_bahan_kemasan]" class="form-control" required>
                    <option value="">Pilih</option>
                    <option value="OK">OK</option>
                    <option value="Tidak OK">Tidak OK</option>
                </select>
            </td>
            <td>
                <textarea name="items[${rowCount}][keterangan]" class="form-control" rows="2"></textarea>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-danger btnRemoveRow"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        renumberRows();
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btnRemoveRow');
        if (!btn) return;

        const rows = document.querySelectorAll('#itemsTable tbody tr.item-row');
        if (rows.length <= 1) {
            return;
        }

        const tr = btn.closest('tr.item-row');
        if (tr) tr.remove();
        renumberRows();
    });
})();
</script>
@endpush
