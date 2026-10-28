@extends('layouts.app')

@section('title', 'Edit Role')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Role</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Role</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit mr-2"></i>Form Edit Role
                            </h3>
                        </div>
                        
                        <form action="{{ route('roles.update', $role->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
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
                                        value="{{ old('role', $role->role) }}" 
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
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save mr-1"></i>Update Role
                                    </button>
                                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Role
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <!-- <tr>
                                    <td><strong>UUID:</strong></td>
                                    <td>
                                        <small class="text-muted">{{ $role->uuid }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat:</strong></td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $role->created_at->format('d/m/Y H:i:s') }}
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate:</strong></td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $role->updated_at->format('d/m/Y H:i:s') }}
                                        </small>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td><strong>Jumlah User:</strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $role->users()->count() }} user
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle mr-1"></i>Perhatian:</h6>
                                <p class="mb-0">
                                    Mengubah nama role akan mempengaruhi semua user yang menggunakan role ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>    
</div>
@endsection