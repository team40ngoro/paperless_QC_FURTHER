@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-emulsi.index') }}">Bahan Emulsi</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Edit Data Bahan Emulsi</h3>
                        </div>
                        <form action="{{ route('bahan-emulsi.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama RM</label>
                                    <input type="text" name="nama_rm" class="form-control" value="{{ $item->nama_rm }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Berat RM (kg)</label>
                                    <input type="text" name="berat_rm" class="form-control" value="{{ $item->berat_rm }}" placeholder="000" required>
                                </div>

                                <!-- Plan -->
                                <div class="form-group">
                                    <label>Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $item->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Produk -->
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="id_produk" id="id_produk" class="form-control" required>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nama Emulsi -->
                                <div class="form-group">
                                    <label>Nama Emulsi</label>
                                    <select name="nama_emulsi_id" id="nama_emulsi_id" class="form-control" required>
                                        <option value="">Pilih Emulsi</option>
                                        @foreach($emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}" data-produk-id="{{ $emulsi->id_produk }}" {{ $item->nama_emulsi_id == $emulsi->id ? 'selected' : '' }}>{{ $emulsi->nama_emulsi }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Total Pemakaian -->
                                <div class="form-group">
                                    <label>Total Pemakaian</label>
                                    <select name="total_pemakaian_id" id="total_pemakaian_id" class="form-control" required>
                                        <option value="">Pilih Total Pemakaian</option>
                                        @foreach($totalPemakaians as $tp)
                                            <option value="{{ $tp->id }}" data-produk-id="{{ $tp->id_produk }}" {{ $item->total_pemakaian_id == $tp->id ? 'selected' : '' }}>{{ $tp->total_pemakaian }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nomor Emulsi -->
                                <div class="form-group">
                                    <label>Nomor Emulsi</label>
                                    <select name="nomor_emulsi_id" id="nomor_emulsi_id" class="form-control" required>
                                        <option value="">Pilih Nomor Emulsi</option>
                                        @foreach($nomorEmulsis as $ne)
                                            <option value="{{ $ne->id }}" data-produk-id="{{ $ne->id_produk }}" data-total-pemakaian-id="{{ $ne->total_pemakaian_id }}" {{ $item->nomor_emulsi_id == $ne->id ? 'selected' : '' }}>{{ $ne->nomor_emulsi }} - {{ $ne->total_pemakaian->total_pemakaian ?? '-' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('bahan-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
function filterDropdowns(produkId) {
    const emulsiSelect = document.getElementById('nama_emulsi_id');
    const emulsiOptions = emulsiSelect.querySelectorAll('option[data-produk-id]');
    emulsiOptions.forEach(opt => {
        opt.style.display = opt.dataset.produkId == produkId ? '' : 'none';
    });
    if (emulsiSelect.value && emulsiSelect.querySelector(`option[value="${emulsiSelect.value}"][data-produk-id]`)?.dataset?.produkId != produkId) {
        emulsiSelect.value = '';
    }

    const tpSelect = document.getElementById('total_pemakaian_id');
    const tpOptions = tpSelect.querySelectorAll('option[data-produk-id]');
    tpOptions.forEach(opt => {
        opt.style.display = opt.dataset.produkId == produkId ? '' : 'none';
    });
    if (tpSelect.value && tpSelect.querySelector(`option[value="${tpSelect.value}"][data-produk-id]`)?.dataset?.produkId != produkId) {
        tpSelect.value = '';
    }

    filterNomorEmulsiEdit();
}

function filterNomorEmulsiEdit() {
    const produkId = document.getElementById('id_produk').value;
    const totalPemakaianId = document.getElementById('total_pemakaian_id').value;

    const neSelect = document.getElementById('nomor_emulsi_id');
    const neOptions = neSelect.querySelectorAll('option[data-produk-id]');
    neOptions.forEach(opt => {
        const matchProduk = !produkId || opt.dataset.produkId == produkId;
        const matchTp = !totalPemakaianId || opt.dataset.totalPemakaianId == totalPemakaianId;
        opt.style.display = matchProduk && matchTp ? '' : 'none';
    });
    if (neSelect.value) {
        const selectedOpt = neSelect.querySelector(`option[value="${neSelect.value}"]`);
        if (selectedOpt && selectedOpt.style.display === 'none') {
            neSelect.value = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Destroy Select2 on nomor_emulsi_id to prevent "loading" issue
    if ($('#nomor_emulsi_id').hasClass('select2-hidden-accessible')) {
        $('#nomor_emulsi_id').select2('destroy');
    }

    const produkSelect = document.getElementById('id_produk');
    const tpSelect = document.getElementById('total_pemakaian_id');

    produkSelect.addEventListener('change', function() {
        filterDropdowns(this.value);
    });

    tpSelect.addEventListener('change', function() {
        filterNomorEmulsiEdit();
    });

    if (produkSelect.value) {
        filterDropdowns(produkSelect.value);
    }
});
</script>
@endpush
@endsection
