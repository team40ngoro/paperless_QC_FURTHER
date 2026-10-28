@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Input Area</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-area.index') }}">Data Input Area</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit text-warning mr-2"></i>
                                Form Edit Data Input Area
                            </h3>
                        </div>
                        <form action="{{ route('input-area.update', $inputArea->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
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
                                        <div class="form-group">
                                            <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_plan') is-invalid @enderror" 
                                                    id="id_plan" name="id_plan" required>
                                                <option value="">Pilih Plan</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" 
                                                            {{ (old('id_plan') ?? $inputArea->id_plan) == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->nama_plan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="area">Area <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('area') is-invalid @enderror" 
                                                   id="area" 
                                                   name="area" 
                                                   value="{{ old('area') ?? $inputArea->area }}" 
                                                   placeholder="Masukkan nama area"
                                                   required>
                                            @error('area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="subarea">Sub Area</label>
                                            <div id="subarea-container">
                                                @foreach($inputArea->subarea as $sub)
                                                    <div class="input-group mb-2">
                                                        <input type="hidden" name="subarea_id[]" value="{{ $sub->id }}">
                                                        <input type="text" name="subarea_name[]" class="form-control" value="{{ $sub->lokasi_area }}" placeholder="Masukkan nama sub area">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove-subarea" type="button">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button id="add-subarea" type="button" class="btn btn-success btn-sm mt-2">
                                                <i class="fas fa-plus"></i> Tambah Sub Area
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="">
                                    <a href="{{ route('input-area.index') }}" class="btn btn-md btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fas fa-save mr-2"></i>Update Data
                                    </button>
                                    <div>
                                        <!-- <a href="{{ route('input-area.show', $inputArea->uuid) }}" class="btn btn-info mr-2">
                                            <i class="fas fa-eye mr-2"></i>Lihat Detail
                                        </a> -->
                                    </div>
                                </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const subareaContainer = document.getElementById('subarea-container');
        const addSubareaBtn = document.getElementById('add-subarea');
        const form = subareaContainer.closest('form');

        addSubareaBtn.addEventListener('click', function () {
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `
                <input type="text" name="new_subarea[]" class="form-control" placeholder="Masukkan nama sub area baru">
                <div class="input-group-append">
                    <button class="btn btn-danger remove-new-subarea" type="button">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            subareaContainer.appendChild(div);
        });

        subareaContainer.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-subarea');
            if (removeBtn) {
                const inputGroup = removeBtn.closest('.input-group');
                const subareaId = inputGroup.querySelector('input[name="subarea_id[]"]').value;
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'delete_subarea[]';
                hiddenInput.value = subareaId;
                form.appendChild(hiddenInput);
                
                inputGroup.remove();
            }

            const removeNewBtn = e.target.closest('.remove-new-subarea');
            if (removeNewBtn) {
                removeNewBtn.closest('.input-group').remove();
            }
        });
    });
</script>
@endpush