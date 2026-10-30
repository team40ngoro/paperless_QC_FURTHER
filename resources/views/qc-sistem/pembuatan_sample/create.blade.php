@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Verifikasi Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('pembuatan-sample') }}">Pembuatan Sample</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Verifikasi</h3>
                </div>
                <form action="{{ route('pembuatan-sample.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        {{-- Header: Shift, Tanggal, Jam --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                    <select name="id_shift" id="id_shift" class="form-control" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Jam <span class="text-danger">*</span></label>
                                    <input type="time" name="jam" class="form-control" value="{{ old('jam', date('H:i')) }}" required>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h4 class="mb-3">Detail Sample</h4>

                        <div id="items-container"></div>

                        <div class="text-center mt-3 mb-3">
                            <button type="button" id="tambah-item" class="btn btn-success">
                                <i class="fas fa-plus-circle"></i> Tambah Data
                            </button>
                            <button type="button" id="hapus-item" class="btn btn-danger ml-2">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                        <a href="{{ route('pembuatan-sample.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var itemIndex = 0;
        var produks = @json($produks);
        var nilaiBeratOptions = ['-', 55, 100, 200, 225, 250, 300, 315, 360, 400, 450, 500, 700, 900, 1000, 1100, 2000];

        function renderItem(index, data) {
            data = data || {};
            var html = `
                <div class="card card-outline card-info mb-3 item-card" data-index="${index}">
                    <div class="card-header py-2">
                        <h5 class="card-title mb-0">Sample #${index + 1}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Produk <span class="text-danger">*</span></label>
                                    <select name="items[${index}][id_produk]" class="form-control produk-select" required>
                                        <option value="">Pilih Produk</option>
                                        ${produks.map(function(p) {
                                            var selected = (data.id_produk && data.id_produk == p.id) ? 'selected' : '';
                                            return '<option value="' + p.id + '" ' + selected + '>' + p.nama_produk + '</option>';
                                        }).join('')}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode Produksi <span class="text-danger">*</span></label>
                                    <input type="text" name="items[${index}][kode_produksi]" class="form-control kode-produksi-input" value="${data.kode_produksi || ''}" placeholder="Contoh: PA01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal Expired <span class="text-danger">*</span></label>
                                    <input type="date" name="items[${index}][tanggal_expired]" class="form-control tanggal-expired-input" value="${data.tanggal_expired || ''}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="items[${index}][jumlah]" class="form-control" value="${data.jumlah || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Gramase (gram) <span class="text-danger">*</span></label>
                                    <select name="items[${index}][berat_sampling]" class="form-control berat-sampling-select" data-nilai-berat="true" required>
                                        <option value="">Pilih Berat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Berat Sampel (gram) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="items[${index}][berat]" class="form-control" value="${data.berat || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jenis Sample <span class="text-danger">*</span></label>
                                    <select name="items[${index}][jenis_sample]" class="form-control" required>
                                        <option value="">Pilih Jenis Sample</option>
                                        <option value="sample rnd" ${data.jenis_sample == 'sample rnd' ? 'selected' : ''}>Sample RND</option>
                                        <option value="sample lab" ${data.jenis_sample == 'sample lab' ? 'selected' : ''}>Sample Lab</option>
                                        <option value="sample trial" ${data.jenis_sample == 'sample trial' ? 'selected' : ''}>Sample Trial</option>
                                        <option value="sample retain" ${data.jenis_sample == 'sample retain' ? 'selected' : ''}>Sample Retain</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return html;
        }

        function renumberItems() {
            $('.item-card').each(function(i) {
                $(this).find('.card-title').text('Sample #' + (i + 1));
            });
        }

        function getLastItemData() {
            var $last = $('.item-card').last();
            if ($last.length === 0) return null;
            var idx = $last.data('index');
            return {
                id_produk: $last.find('select[name="items[' + idx + '][id_produk]"]').val(),
                kode_produksi: $last.find('input[name="items[' + idx + '][kode_produksi]"]').val(),
                tanggal_expired: $last.find('input[name="items[' + idx + '][tanggal_expired]"]').val(),
                jumlah: $last.find('input[name="items[' + idx + '][jumlah]"]').val(),
                berat_sampling: $last.find('select[name="items[' + idx + '][berat_sampling]"]').val(),
                berat: $last.find('input[name="items[' + idx + '][berat]"]').val(),
                jenis_sample: $last.find('select[name="items[' + idx + '][jenis_sample]"]').val()
            };
        }

        function addItem(data) {
            // FIX: normalisasi data di awal supaya semua "data.xxx" di bawah aman
            // diakses walaupun addItem() dipanggil tanpa argumen (misalnya saat load pertama kali).
            data = data || {};

            var html = renderItem(itemIndex, data);
            var $card = $(html).appendTo('#items-container');
            $card.find('.produk-select').select2({ theme: 'bootstrap4', placeholder: 'Pilih Produk', width: '100%' });

            var $beratSelect = $card.find('.berat-sampling-select');
            $.each(nilaiBeratOptions, function(i, val) {
                $beratSelect.append('<option value="' + val + '">' + val + '</option>');
            });
            $beratSelect.select2({ theme: 'bootstrap4', placeholder: 'Pilih Berat', width: '100%' });

            if (data.berat_sampling) {
                $beratSelect.val(String(data.berat_sampling)).trigger('change');
            }

            itemIndex++;
            renumberItems();
        }

        // Tambah item pertama saat halaman dimuat
        addItem();

        // Tombol "Tambah Data": duplikat nilai dari item terakhir ke item baru
        $('#tambah-item').on('click', function() {
            var lastData = getLastItemData();
            addItem(lastData || {});
        });

        // Tombol "Hapus Data": hapus item terakhir yang ditambahkan
        $('#hapus-item').on('click', function() {
            var $items = $('.item-card');
            if ($items.length <= 1) {
                alert('Minimal harus ada 1 data sample.');
                return;
            }
            $items.last().remove();
            renumberItems();
        });

        // Hook opsional: auto-hitung tanggal expired dari kode produksi (jika fungsi tersedia)
        $(document).on('input', '.kode-produksi-input', function() {
            var $input = $(this);
            var $expired = $input.closest('.item-card').find('.tanggal-expired-input');
            if (typeof setupKodeProduksi === 'function') {
                setupKodeProduksi($input, $expired);
            }
        });
    });
</script>
@endpush

@endsection