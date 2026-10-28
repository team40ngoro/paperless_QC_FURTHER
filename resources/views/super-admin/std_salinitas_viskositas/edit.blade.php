{{-- filepath: resources/views/super-admin/std_salinitas_viskositas/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Std Salinitas & Viskositas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('std-salinitas-viskositas.index') }}">Std Salinitas & Viskositas</a></li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Data Std Salinitas & Viskositas</h3>
                        </div>
                        <form action="{{ route('std-salinitas-viskositas.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan">Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('id_plan', $item->id_plan) == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="id_produk">Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control @error('id_produk') is-invalid @enderror" required>
                                        <option value="">Pilih Produk</option>
                                        {{-- Populated by AJAX --}}
                                    </select>
                                    @error('id_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="id_better">Better</label>
                                    <select name="id_better" id="id_better_select" class="form-control @error('id_better') is-invalid @enderror" required>
                                        <option value="">Pilih Better</option>
                                        {{-- Populated by AJAX --}}
                                    </select>
                                    @error('id_better')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="std_viskositas">Std Viskositas</label>
                                    <input type="text" name="std_viskositas" id="std_viskositas" class="form-control @error('std_viskositas') is-invalid @enderror" value="{{ old('std_viskositas', $item->std_viskositas) }}" required>
                                    @error('std_viskositas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="std_salinitas">Std Salinitas</label>
                                    <input type="text" name="std_salinitas" id="std_salinitas" class="form-control @error('std_salinitas') is-invalid @enderror" value="{{ old('std_salinitas', $item->std_salinitas) }}" required>
                                    @error('std_salinitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="std_suhu_akhir">Std Suhu Air</label>
                                    <input type="text" name="std_suhu_akhir" id="std_suhu_akhir" class="form-control @error('std_suhu_akhir') is-invalid @enderror" value="{{ old('std_suhu_akhir', $item->std_suhu_akhir) }}" required>
                                    @error('std_suhu_akhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('std-salinitas-viskositas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
        function fetchProduk(planId, selectedProdukId) {
            if (planId) {
                $.ajax({
                    url: '/get-produk-by-plan/' + planId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#id_produk').empty().append('<option value="">Pilih Produk</option>');
                        $.each(data, function(key, value) {
                            $('#id_produk').append('<option value="' + key + '"' + (key == selectedProdukId ? ' selected' : '') + '>' + value + '</option>');
                        });
                        if (selectedProdukId) {
                            $('#id_produk').val(selectedProdukId).trigger('change');
                        }
                    }
                });
            } else {
                $('#id_produk').empty().append('<option value="">Pilih Produk</option>');
                $('#id_better').empty().append('<option value="">Pilih Better</option>');
            }
        }

        function fetchBetter(produkId, selectedBetterId) {
            if (produkId) {
                $.ajax({
                    url: '/get-better-by-produk/' + produkId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#id_better').empty().append('<option value="">Pilih Better</option>');
                        $.each(data, function(key, value) {
                            $('#id_better').append('<option value="' + key + '"' + (key == selectedBetterId ? ' selected' : '') + '>' + value + '</option>');
                        });
                        if (selectedBetterId) {
                            $('#id_better').val(selectedBetterId);
                        }
                    }
                });
            } else {
                $('#id_better').empty().append('<option value="">Pilih Better</option>');
            }
        }

        // Initial load
        var initialPlanId = '{{ old('id_plan', $item->id_plan) }}';
        var initialProdukId = '{{ old('id_produk', $item->id_produk) }}';
        var initialBetterId = '{{ old('id_better', $item->id_better) }}';

        if (initialPlanId) {
            fetchProduk(initialPlanId, initialProdukId);
        }

        // Event listeners
        $('#id_plan').change(function() {
            var planId = $(this).val();
            fetchProduk(planId, null);
        });

        $('#id_produk').change(function() {
            var produkId = $(this).val();
            // Fetch better with the correct pre-selected value only if the produk is the initial one
            var selectedBetterId = (produkId == initialProdukId) ? initialBetterId : null;
            fetchBetter(produkId, selectedBetterId);
        });
    });
</script>
@endpush