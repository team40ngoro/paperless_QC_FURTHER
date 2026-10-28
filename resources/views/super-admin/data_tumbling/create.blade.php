@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('data-tumbling.index') }}">Data Tumbling</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-drum"></i> Form Tambah Data Tumbling
                                </h3>
                            </div>
                            <form action="{{ route('data-tumbling.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    @if(session('info'))
                                        <div class="alert alert-info alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <i class="icon fas fa-info-circle"></i> {{ session('info') }}
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <i class="icon fas fa-exclamation-triangle"></i>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-secondary">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i> Informasi Produk
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="id_plan">
                                                            <i class="fas fa-drum"></i> Plan
                                                        </label>
                                                        <select class="form-control @error('id_plan') is-invalid @enderror" 
                                                                id="id_plan" name="id_plan" required>
                                                            <option value="">Pilih Plan</option>
                                                            @foreach($plans as $plan)
                                                                <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                                                                    {{ $plan->nama_plan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('id_plan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="id_produk">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                                id="id_produk" name="id_produk" required>
                                                            <option value="">Pilih Produk</option>
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
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Parameter Tumbling -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card card-outline card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-cogs"></i> Parameter Tumbling (Vakum)
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="drum_on">
                                                            <i class="fas fa-play"></i> Drum On (menit)
                                                        </label>
                                                        <input type="text" class="form-control @error('drum_on') is-invalid @enderror" 
                                                               id="drum_on" name="drum_on" value="{{ old('drum_on') }}" required>
                                                        @error('drum_on')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="drum_off">
                                                            <i class="fas fa-stop"></i> Drum Off (menit)
                                                        </label>
                                                        <input type="text" class="form-control @error('drum_off') is-invalid @enderror" 
                                                               id="drum_off" name="drum_off" value="{{ old('drum_off') }}" required>
                                                        @error('drum_off')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="drum_speed">
                                                            <i class="fas fa-tachometer-alt"></i> Drum Speed (%)
                                                        </label>
                                                        <input type="text" class="form-control @error('drum_speed') is-invalid @enderror" 
                                                               id="drum_speed" name="drum_speed" value="{{ old('drum_speed') }}" required>
                                                        @error('drum_speed')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="total_waktu">
                                                            <i class="fas fa-clock"></i> Total Waktu (menit)
                                                        </label>
                                                        <input type="text" class="form-control @error('total_waktu') is-invalid @enderror" 
                                                               id="total_waktu" name="total_waktu" value="{{ old('total_waktu') }}" required>
                                                        @error('total_waktu')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="tekanan_vakum">
                                                            <i class="fas fa-compress-arrows-alt"></i> Tekanan Vakum (%)
                                                        </label>
                                                        <input type="text" class="form-control @error('tekanan_vakum') is-invalid @enderror" 
                                                               id="tekanan_vakum" name="tekanan_vakum" value="{{ old('tekanan_vakum') }}" required>
                                                        @error('tekanan_vakum')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card card-outline card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-cogs"></i> Parameter Tumbling (Non Vakum)
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="drum_on_non_vakum">
                                                            <i class="fas fa-play"></i> Drum On (menit)
                                                        </label>
                                                        <input type="text" class="form-control @error('drum_on_non_vakum') is-invalid @enderror" 
                                                               id="drum_on_non_vakum" name="drum_on_non_vakum" value="{{ old('drum_on_non_vakum') }}">
                                                        @error('drum_on_non_vakum')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="drum_off_non_vakum">
                                                            <i class="fas fa-stop"></i> Drum Off (menit)
                                                        </label>
                                                        <input type="text" class="form-control @error('drum_off_non_vakum') is-invalid @enderror" 
                                                               id="drum_off_non_vakum" name="drum_off_non_vakum" value="{{ old('drum_off_non_vakum') }}">
                                                        @error('drum_off_non_vakum')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="drum_speed_non_vakum">
                                                            <i class="fas fa-tachometer-alt"></i> Drum Speed (%)
                                                        </label>
                                                        <input type="text" class="form-control @error('drum_speed_non_vakum') is-invalid @enderror" 
                                                               id="drum_speed_non_vakum" name="drum_speed_non_vakum" value="{{ old('drum_speed_non_vakum') }}">
                                                        @error('drum_speed_non_vakum')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="total_waktu_non_vakum">
                                                            <i class="fas fa-clock"></i> Total Waktu (menit)
                                                        </label>
                                                        <input type="text" class="form-control @error('total_waktu_non_vakum') is-invalid @enderror" 
                                                               id="total_waktu_non_vakum" name="total_waktu_non_vakum" value="{{ old('total_waktu_non_vakum') }}">
                                                        @error('total_waktu_non_vakum')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="tekanan_non_vakum">
                                                            <i class="fas fa-compress-arrows-alt"></i> Tekanan Non Vakum (%)
                                                        </label>
                                                        <input type="text" class="form-control @error('tekanan_non_vakum') is-invalid @enderror" 
                                                               id="tekanan_non_vakum" name="tekanan_non_vakum" value="{{ old('tekanan_non_vakum') }}">
                                                        @error('tekanan_non_vakum')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="{{ route('data-tumbling.index') }}" class="btn btn-secondary">
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
</div>
@endsection