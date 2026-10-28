@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Pemeriksaan Bahan Kemas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-bahan-kemas.index') }}">Pemeriksaan Bahan Kemas</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                                <i class="fas fa-edit text-primary mr-2"></i>
                                Form Edit Pemeriksaan Bahan Kemas
                            </h3>
                        </div>
                        <form action="{{ route('pemeriksaan-bahan-kemas.update', $item->uuid) }}" method="POST">
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
                                        {{-- dev_bagus_perbaikan_29_mei:remove disable form--}}
                                        <div class="form-group mb-3">
                                            <label for="tanggal_display" class="font-weight-bold">
                                                Tanggal <span class="text-danger">*</span>
                                            </label>

                                            <input type="date"
                                                id="tanggal_display"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                value="{{ old('tanggal', $item->tanggal ? $item->tanggal->format('Y-m-d') : '') }}"
                                                onchange="
                                                    const t = document.getElementById('tanggal').value.split(' ')[1];
                                                    const [y,m,d] = this.value.split('-');
                                                    document.getElementById('tanggal').value = `${d}-${m}-${y} ${t}`;
                                                "
                                                required>

                                            <input type="hidden"
                                                id="tanggal"
                                                name="tanggal"
                                                value="{{ old('tanggal', $item->tanggal ? $item->tanggal->format('d-m-Y H:i:s') : '') }}">

                                            @error('tanggal')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('jam') is-invalid @enderror" id="jam" name="jam" value="{{ old('jam', $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '') }}" required>
                                            @error('jam')
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
                                                    <option value="{{ $shift->id }}" {{ old('shift_id', $item->shift_id) == $shift->id ? 'selected' : '' }}>
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nama_kemasan" class="font-weight-bold">Nama Kemasan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama_kemasan') is-invalid @enderror" id="nama_kemasan" name="nama_kemasan" value="{{ old('nama_kemasan', $item->nama_kemasan) }}" required>
                                            @error('nama_kemasan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="kode_produksi" class="font-weight-bold">Kode Produksi <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('kode_produksi') is-invalid @enderror" id="kode_produksi" name="kode_produksi" value="{{ old('kode_produksi', $item->kode_produksi) }}" required>
                                            @error('kode_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="kondisi_bahan_kemasan" class="font-weight-bold">Kondisi Bahan Kemasan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kondisi_bahan_kemasan') is-invalid @enderror" id="kondisi_bahan_kemasan" name="kondisi_bahan_kemasan" required>
                                                <option value="">Pilih Kondisi</option>
                                                <option value="OK" {{ old('kondisi_bahan_kemasan', $item->kondisi_bahan_kemasan) == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('kondisi_bahan_kemasan', $item->kondisi_bahan_kemasan) == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('kondisi_bahan_kemasan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="keterangan" class="font-weight-bold">Keterangan</label>
                                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="4">{{ old('keterangan', $item->keterangan) }}</textarea>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update
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
