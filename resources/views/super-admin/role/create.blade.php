@extends('layouts.app')

@section('title', 'Tambah Role')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Role</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Role</a></li>
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
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plus mr-2"></i>Form Tambah Role
                                </h3>
                            </div>
                            
                            <form action="{{ route('roles.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="role" class="form-label">
                                            <i class="fas fa-user-tag mr-1"></i>Nama Role
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                            class="form-control @error('role') is-invalid @enderror" 
                                            id="role" 
                                            name="role" 
                                            value="{{ old('role') }}" 
                                            placeholder="Masukkan nama role (contoh: admin, user, manager)"
                                            required>
                                        @error('role')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Nama role harus unik dan akan digunakan untuk mengatur hak akses user
                                        </small>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Simpan Role
                                        </button>
                                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                                        </a>
                                    </div>
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