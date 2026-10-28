@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-emulsi.index') }}">Bahan Emulsi</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                            <h3 class="card-title">Tambah Data Bahan Emulsi</h3>
                        </div>
                            <form action="{{ route('bahan-emulsi.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group mb-2">
                                        <label>Nama Plan</label>
                                        <select name="id_plan" id="id_plan_select" class="form-control" required>
                                            <option value="">Pilih Plan</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Nama Produk</label>
                                        <select name="id_produk" id="id_produk_select" class="form-control" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Nama Emulsi</label>
                                        <select name="nama_emulsi_id" id="nama_emulsi_id" class="form-control" required>
                                            <option value="">Pilih Emulsi</option>
                                            @foreach($emulsis as $emulsi)
                                                <option value="{{ $emulsi->id }}" data-produk-id="{{ $emulsi->id_produk }}">{{ $emulsi->nama_emulsi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Total Pemakaian</label>
                                        <select name="total_pemakaian_id" id="total_pemakaian_id" class="form-control" required>
                                            <option value="">Pilih Total Pemakaian</option>
                                            @foreach($totalPemakaians as $tp)
                                                <option value="{{ $tp->id }}" data-produk-id="{{ $tp->id_produk }}">{{ $tp->total_pemakaian }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Proses Emulsi</label>
                                        <select name="nomor_emulsi_id" id="nomor_emulsi_id" class="form-control" required>
                                            <option value="">Pilih Nomor Emulsi</option>
                                            @foreach($nomorEmulsis as $ne)
                                                <option value="{{ $ne->id }}" data-produk-id="{{ $ne->id_produk }}" data-total-pemakaian-id="{{ $ne->total_pemakaian_id }}">{{ $ne->nomor_emulsi }} - {{ $ne->total_pemakaian->total_pemakaian ?? '-' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                <!-- Dynamic Form untuk Nama RM dan Berat RM -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Bahan Raw Material</h5>
                                        <button type="button" class="btn btn-success btn-sm float-right" id="addRM">
                                            <i class="fas fa-plus"></i> Tambah RM
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="rm-container">
                                            <div class="rm-item mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label>Nama RM</label>
                                                        <input type="text" name="nama_rm[]" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Berat RM (kg)</label>
                                                        <input type="text" name="berat_rm[]" class="form-control" placeholder="000" required>                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block remove-rm" disabled>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                                <a href="{{ route('bahan-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
function filterCreateDropdowns(produkId) {
    const emulsiSelect = document.getElementById('nama_emulsi_id');
    emulsiSelect.querySelectorAll('option[data-produk-id]').forEach(opt => {
        opt.style.display = opt.dataset.produkId == produkId ? '' : 'none';
    });
    if (emulsiSelect.value && emulsiSelect.querySelector(`option[value="${emulsiSelect.value}"][data-produk-id]`)?.dataset?.produkId != produkId) {
        emulsiSelect.value = '';
    }

    const tpSelect = document.getElementById('total_pemakaian_id');
    tpSelect.querySelectorAll('option[data-produk-id]').forEach(opt => {
        opt.style.display = opt.dataset.produkId == produkId ? '' : 'none';
    });
    if (tpSelect.value && tpSelect.querySelector(`option[value="${tpSelect.value}"][data-produk-id]`)?.dataset?.produkId != produkId) {
        tpSelect.value = '';
    }

    filterNomorEmulsi();
}

function filterNomorEmulsi() {
    const produkId = document.getElementById('id_produk_select').value;
    const totalPemakaianId = document.getElementById('total_pemakaian_id').value;

    const neSelect = document.getElementById('nomor_emulsi_id');
    neSelect.querySelectorAll('option[data-produk-id]').forEach(opt => {
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
    if ($('#nomor_emulsi_id').hasClass('select2-hidden-accessible')) {
        $('#nomor_emulsi_id').select2('destroy');
    }

    const produkSelect = document.getElementById('id_produk_select');
    const tpSelect = document.getElementById('total_pemakaian_id');

    produkSelect.addEventListener('change', function() {
        filterCreateDropdowns(this.value);
    });

    tpSelect.addEventListener('change', function() {
        filterNomorEmulsi();
    });

    if (produkSelect.value) {
        filterCreateDropdowns(produkSelect.value);
    }

    let rmIndex = 1;
    
    document.getElementById('addRM').addEventListener('click', function() {
        const container = document.getElementById('rm-container');
        const newRM = `
            <div class="rm-item mb-3" data-index="${rmIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label>Nama RM</label>
                        <input type="text" name="nama_rm[]" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label>Berat RM (kg)</label>
                        <input type="number" step="0.01" name="berat_rm[]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-rm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRM);
        rmIndex++;
        updateRemoveButtons();
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-rm') || e.target.closest('.remove-rm')) {
            const rmItem = e.target.closest('.rm-item');
            rmItem.remove();
            updateRemoveButtons();
        }
    });
    
    function updateRemoveButtons() {
        const rmItems = document.querySelectorAll('.rm-item');
        document.querySelectorAll('.remove-rm').forEach(button => {
            button.disabled = rmItems.length <= 1;
        });
    }
});
</script>
@endsection