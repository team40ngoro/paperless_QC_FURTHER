{{-- filepath: resources/views/super-admin/data_shift/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Shift</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('data-shift.index') }}">Data Shift</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Data Shift</h3>
                        </div>
                        <form action="{{ route('data-shift.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan">Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $item->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="shift">Shift</label>
                                    <input type="text" name="shift" id="shift" class="form-control" value="{{ $item->shift }}" placeholder="Masukkan Shift" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('data-shift.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
