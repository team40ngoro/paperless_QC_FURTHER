{{-- filepath: resources/views/super-admin/user/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-3 border-0">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-user-edit me-2"></i>Edit User
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('users.update', $user->uuid) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-user me-2 text-primary"></i> Username
                            </label>
                            <input type="text" name="name" class="form-control border-2 rounded-3" 
                                   value="{{ old('name', $user->name) }}" required 
                                   placeholder="Enter username">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-at me-2 text-primary"></i> Username
                            </label>
                            <input type="text" name="username" class="form-control border-2 rounded-3" 
                                   value="{{ old('username', $user->username) }}" required 
                                   placeholder="Enter username">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-envelope me-2 text-primary"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control border-2 rounded-3" 
                                   value="{{ old('email', $user->email) }}" required 
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-lock me-2 text-primary"></i> Password Baru 
                                <small class="text-muted fw-normal">(kosongkan jika tidak ganti)</small>
                            </label>
                            <input type="password" name="password" class="form-control border-2 rounded-3" 
                                   placeholder="Enter new password">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-users-cog me-2 text-primary"></i> Role Baru (Database)
                            </label>
                            <select name="id_role" class="form-select form-control form-select-lg border-2 rounded-3">
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->id_role == $role->id ? 'selected' : '' }}>
                                        <i class="fas fa-user-tag"></i> {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-clipboard-list me-2 text-primary"></i> Plan
                            </label>
                            <select name="id_plan" class="form-select form-control form-select-lg border-2 rounded-3" required>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ $user->getRawOriginal('id_plan') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->nama_plan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-md">
                                <i class="fas fa-save me-2"></i> Update User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-md ml-2">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection