@extends('layouts.app')

@section('title', 'Edit Master CIP')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">Edit Master CIP</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('master-cip.index') }}">Master CIP</a></li>
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
              <div class="card-header"><h3 class="card-title">Edit Standar Tahap CIP</h3></div>
              <form method="POST" action="{{ route('master-cip.update', $master->uuid) }}">
                @csrf @method('PUT')
                <div class="card-body">

                  @if($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                  @endif

                  <div class="form-group">
                    <label>Plan</label>
                    <input type="text" class="form-control" value="{{ optional($master->plan)->nama_plan ?? '-' }}" disabled>
                  </div>

                  <div class="form-group">
                    <label>Tahap CIP</label>
                    <input type="text" class="form-control"
                           value="{{ $master->step_label ?? $steps[$master->step_key] ?? $master->step_key }}" disabled>
                    <small class="text-muted">Kode: <code>{{ $master->step_key }}</code></small>
                  </div>

                  <div class="form-group">
                    <label>Standar Waktu (detik)</label>
                    <div class="input-group">
                      <input type="number" name="std_waktu" class="form-control" step="1" min="0"
                             value="{{ old('std_waktu', $master->std_waktu) }}" placeholder="Contoh: 120">
                      <div class="input-group-append"><span class="input-group-text">dtk</span></div>
                    </div>
                  </div>



                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
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
