@extends('layouts.app')

@section('title', 'Tambah Master CIP')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">Tambah Master CIP</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('master-cip.index') }}">Master CIP</a></li>
              <li class="breadcrumb-item active">Tambah</li>
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
              <div class="card-header"><h3 class="card-title">Form Tambah Standar Tahap CIP</h3></div>
              <form method="POST" action="{{ route('master-cip.store') }}">
                @csrf
                <div class="card-body">

                  @if($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                  @endif

                  @auth
                    @if(auth()->user()->role === 'superadmin')
                      <div class="form-group">
                        <label>Plan <span class="text-danger">*</span></label>
                        <select name="id_plan" class="form-control" required>
                          <option value="">-- Pilih Plan --</option>
                          @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                              {{ $plan->nama_plan }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    @endif
                  @endauth

                  <div class="form-group">
                    <label>Tahap CIP <span class="text-danger">*</span></label>
                    <select name="step_key" class="form-control" required>
                      <option value="">-- Pilih Tahap --</option>
                      @foreach($steps as $key => $label)
                        <option value="{{ $key }}" {{ old('step_key') === $key ? 'selected' : '' }}>
                          {{ $label }} ({{ $key }})
                        </option>
                      @endforeach
                    </select>
                    <small class="text-muted">Setiap tahap hanya boleh memiliki 1 data per plan.</small>
                  </div>

                  <div class="form-group">
                    <label>Standar Waktu (detik)</label>
                    <div class="input-group">
                      <input type="number" name="std_waktu" class="form-control" step="1" min="0"
                             value="{{ old('std_waktu') }}" placeholder="Contoh: 120">
                      <div class="input-group-append"><span class="input-group-text">dtk</span></div>
                    </div>
                  </div>



                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                  <a href="{{ route('master-cip.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
@endsection
