@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manajemen Role</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Role</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users-cog mr-2"></i>Daftar Role
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>Tambah Role
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="table text-center table-bordered table-striped table-hover">
                            <thead class="">
                                <tr>
                                    <th>No</th>
                                    <!-- <th width="15%">UUID</th> -->
                                    <th>Nama Role</th>
                                    <th>Jumlah User</th>
                                    <!-- <th>Dibuat</th> -->
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $index => $role)
                                    <tr>
                                        <td>{{ $roles->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge badge-primary" style="text-transform:uppercase;">
                                                <i class=""></i>{{ $role->role_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <i class="fas fa-users mr-1"></i>{{ $role->users_count }} user
                                            </span>
                                        </td>
                                        <!-- <td>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $role->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td> -->
                                        <td>
                                            <div class="btn-vertical">
                                                <!-- <a href="{{ route('roles.show', $role->uuid) }}" 
                                                class="btn btn-info btn-sm" 
                                                title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a> -->
                                                <a href="{{ route('roles.edit', $role->uuid) }}" 
                                                class="btn btn-warning btn-sm" 
                                                title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('roles.destroy', $role->uuid) }}" 
                                                    method="POST" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Hapus"
                                                            {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <br>Belum ada data role
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($roles->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan {{ $roles->firstItem() }} - {{ $roles->lastItem() }} 
                                dari {{ $roles->total() }} data
                            </div>
                            {{ $roles->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    </div>
</div>
@endsection