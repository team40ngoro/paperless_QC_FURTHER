@extends('layouts.app')

@section('title', 'Tambah Pemeriksaan Proses Thawing')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Pemeriksaan Proses Thawing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-twahing.index') }}">Proses Thawing</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Form Pemeriksaan Proses Thawing</h3>
                                <div class="card-tools">
                                    <a href="{{ route('proses-twahing.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>

                            <form action="{{ route('proses-twahing.store') }}" method="POST">
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
                                        @if(auth()->user()->role === 'superadmin')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" onchange="window.location='{{ route('proses-twahing.create') }}?plan_id='+this.value" required>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}" {{ (int) $selectedPlanId === (int) $plan->id ? 'selected' : '' }}>
                                                                {{ $plan->nama_plan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_plan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                                <select name="id_produk" id="id_produk" class="form-control select2-header @error('id_produk') is-invalid @enderror" required>
                                                    <option value="">-- pilih produk --</option>
                                                    @foreach($produks as $produk)
                                                        <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                            {{ $produk->nama_produk }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_produk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tanggal">Hari/Tanggal <span class="text-danger">*</span></label>
                                                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}" required>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jam">Jam</label>
                                                <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}">
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                                <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                    <option value="">-- pilih shift --</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>
                                                            Shift {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_shift')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="waktu_thawing_awal">Waktu Thawing (Awal)</label>
                                                <input type="time" name="waktu_thawing_awal" id="waktu_thawing_awal" class="form-control" value="{{ old('waktu_thawing_awal') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="waktu_thawing_akhir">Waktu Thawing (Akhir)</label>
                                                <input type="time" name="waktu_thawing_akhir" id="waktu_thawing_akhir" class="form-control" value="{{ old('waktu_thawing_akhir') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="total_waktu_thawing_jam">Total Waktu Thawing (Jam)</label>
                                                <input type="number" step="1" name="total_waktu_thawing_jam" id="total_waktu_thawing_jam" class="form-control" value="{{ old('total_waktu_thawing_jam') }}" placeholder="Contoh: 2">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="catatan">Catatan</label>
                                                <textarea name="catatan" id="catatan" rows="2" class="form-control">{{ old('catatan') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h5 class="mb-2">Detail Pemeriksaan</h5>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="detailTable" style="white-space:nowrap;">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="width:60px;">No</th>
                                                    <th>Nama RM</th>
                                                    <th>Kode Produksi</th>
                                                    <th>Kondisi Ruang</th>
                                                    <th>Waktu Pemeriksaan</th>
                                                    <th>Suhu RM (°C)</th>
                                                    <th>Suhu Ruang (°C)</th>
                                                    <th>Suhu Air Thawing (°C)</th>
                                                    <th>Suhu Produk (°C)</th>
                                                    <th>Kondisi Produk</th>
                                                    <th style="width:80px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $oldDetails = old('details', [
                                                        [
                                                            'id_rm' => null,
                                                            'kode_produksi' => null,
                                                            'kondisi_ruang' => null,
                                                            'waktu_pemeriksaan' => null,
                                                            'suhu_rm' => null,
                                                            'suhu_ruang' => null,
                                                            'suhu_air_thawing' => null,
                                                            'suhu_produk' => null,
                                                            'kondisi_produk' => null,
                                                        ]
                                                    ]);
                                                @endphp

                                                @foreach($oldDetails as $i => $d)
                                                    <tr>
                                                        <td class="text-center align-middle row-no">{{ $i + 1 }}</td>
                                                        <td>
                                                            <select name="details[{{ $i }}][id_rm]" class="form-control select2-rm">
                                                                <option value="">-- pilih RM --</option>
                                                                @foreach($rms as $rm)
                                                                    <option value="{{ $rm->id }}" {{ (string) ($d['id_rm'] ?? '') === (string) $rm->id ? 'selected' : '' }}>
                                                                        {{ $rm->nama_rm }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="details[{{ $i }}][kode_produksi]" class="form-control" value="{{ $d['kode_produksi'] ?? '' }}"></td>
                                                        <td>
                                                            <select name="details[{{ $i }}][kondisi_ruang]" class="form-control">
                                                                <option value="">-- pilih --</option>
                                                                <option value="1. Bersih, tidak ada dripping atau kondensasi" {{ ($d['kondisi_ruang'] ?? '') === '1. Bersih, tidak ada dripping atau kondensasi' ? 'selected' : '' }}>1. Bersih, tidak ada dripping atau kondensasi</option>
                                                                <option value="2. Bersih, tidak ada kontaminasi benda asing atau kotoran yang menempel; tidak tercium bau menyimpang" {{ ($d['kondisi_ruang'] ?? '') === '2. Bersih, tidak ada kontaminasi benda asing atau kotoran yang menempel; tidak tercium bau menyimpang' ? 'selected' : '' }}>2. Bersih, tidak ada kontaminasi benda asing atau kotoran yang menempel; tidak tercium bau menyimpang</option>
                                                                <option value="3. Ada dripping dan kondensasi" {{ ($d['kondisi_ruang'] ?? '') === '3. Ada dripping dan kondensasi' ? 'selected' : '' }}>3. Ada dripping dan kondensasi</option>
                                                                <option value="4. Ada kontaminan" {{ ($d['kondisi_ruang'] ?? '') === '4. Ada kontaminan' ? 'selected' : '' }}>4. Ada kontaminan</option>
                                                                <option value="5. Bau dan sensori tidak sesuai/ menyimpang" {{ ($d['kondisi_ruang'] ?? '') === '5. Bau dan sensori tidak sesuai/ menyimpang' ? 'selected' : '' }}>5. Bau dan sensori tidak sesuai/ menyimpang</option>
                                                                <option value="6. Warna tidak sesuai/ menyimpang" {{ ($d['kondisi_ruang'] ?? '') === '6. Warna tidak sesuai/ menyimpang' ? 'selected' : '' }}>6. Warna tidak sesuai/ menyimpang</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="time" name="details[{{ $i }}][waktu_pemeriksaan]" class="form-control" value="{{ $d['waktu_pemeriksaan'] ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_rm]" class="form-control" value="{{ $d['suhu_rm'] ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_ruang]" class="form-control" value="{{ $d['suhu_ruang'] ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_air_thawing]" class="form-control" value="{{ $d['suhu_air_thawing'] ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_produk]" class="form-control" value="{{ $d['suhu_produk'] ?? '' }}"></td>
                                                        <td><input type="text" name="details[{{ $i }}][kondisi_produk]" class="form-control" value="{{ $d['kondisi_produk'] ?? '' }}"></td>
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" class="btn btn-success btn-sm" id="btnAddRow">
                                        <i class="fas fa-plus"></i> Tambah Baris
                                    </button>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSelect2Rm($ctx) {
        const $el = ($ctx && $ctx.length) ? $ctx.find('.select2-rm') : $('.select2-rm');
        $el.each(function () {
            const $s = $(this);
            if ($s.hasClass('select2-hidden-accessible')) {
                return;
            }
            $s.select2({
                placeholder: '-- pilih RM --',
                allowClear: true,
                width: '100%'
            });
        });
    }

    function renumber() {
        $('#detailTable tbody tr').each(function (idx) {
            $(this).find('.row-no').text(idx + 1);
            $(this).find('select, input').each(function () {
                const name = $(this).attr('name');
                if (!name) return;
                $(this).attr('name', name.replace(/details\[\d+\]/, 'details[' + idx + ']'));
            });
        });
    }

    $(document).ready(function () {
        $('.select2-header').select2({
            placeholder: '-- pilih produk --',
            allowClear: true,
            width: '100%'
        });
        initSelect2Rm();

        function calcTotalThawingJam() {
            const awal = $('#waktu_thawing_awal').val();
            const akhir = $('#waktu_thawing_akhir').val();
            if (awal && akhir) {
                const [h1, m1] = awal.split(':').map(Number);
                const [h2, m2] = akhir.split(':').map(Number);
                let t1 = h1 * 60 + m1;
                let t2 = h2 * 60 + m2;
                if (t2 < t1) t2 += 24 * 60;
                const diffHours = Math.round((t2 - t1) / 60);
                $('#total_waktu_thawing_jam').val(diffHours);
            }
        }
        $('#waktu_thawing_awal, #waktu_thawing_akhir').on('change', calcTotalThawingJam);
    });

    $('#btnAddRow').on('click', function () {
        const $tbody = $('#detailTable tbody');
        const idx = $tbody.find('tr').length;

        const rowHtml = `
<tr>
    <td class="text-center align-middle row-no">${idx + 1}</td>
    <td>
        <select name="details[${idx}][id_rm]" class="form-control select2-rm">
            <option value="">-- pilih RM --</option>
            @foreach($rms as $rm)
                <option value="{{ $rm->id }}">{{ $rm->nama_rm }}</option>
            @endforeach
        </select>
    </td>
    <td><input type="text" name="details[${idx}][kode_produksi]" class="form-control"></td>
    <td>
        <select name="details[${idx}][kondisi_ruang]" class="form-control">
            <option value="">-- pilih --</option>
            <option value="1. Bersih, tidak ada dripping atau kondensasi">1. Bersih, tidak ada dripping atau kondensasi</option>
            <option value="2. Bersih, tidak ada kontaminasi benda asing atau kotoran yang menempel; tidak tercium bau menyimpang">2. Bersih, tidak ada kontaminasi benda asing atau kotoran yang menempel; tidak tercium bau menyimpang</option>
            <option value="3. Ada dripping dan kondensasi">3. Ada dripping dan kondensasi</option>
            <option value="4. Ada kontaminan">4. Ada kontaminan</option>
            <option value="5. Bau dan sensori tidak sesuai/ menyimpang">5. Bau dan sensori tidak sesuai/ menyimpang</option>
            <option value="6. Warna tidak sesuai/ menyimpang">6. Warna tidak sesuai/ menyimpang</option>
        </select>
    </td>
    <td><input type="time" name="details[${idx}][waktu_pemeriksaan]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_rm]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_ruang]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_air_thawing]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_produk]" class="form-control"></td>
    <td><input type="text" name="details[${idx}][kondisi_produk]" class="form-control"></td>
    <td class="text-center align-middle">
        <button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>`;

        $tbody.append(rowHtml);
        renumber();
        initSelect2Rm($tbody);
    });

    $(document).on('click', '.btn-remove-row', function () {
        const $tbody = $('#detailTable tbody');
        if ($tbody.find('tr').length <= 1) {
            $tbody.find('tr').find('input').val('');
            $tbody.find('tr').find('select').val('').trigger('change');
            return;
        }

        $(this).closest('tr').remove();
        renumber();
    });
})();
</script>
@endpush
