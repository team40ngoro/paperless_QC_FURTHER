@extends('layouts.app')
@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h1 class="text-dark">Form Profile</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="col-md-12 mb-lg-0">
            <div class="card card-primary card-outline">
                <div class="card-header pb-0 p-3 ">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <span>
                            <h3 class="card-title"><i class="fas fa-plus"></i> Tambah Akun</h3>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                <input type="text" name="name" class="form-control mb-0 no-uppercase" placeholder="Nama Lengkap" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                <input type="text" name="username" class="form-control mb-0 no-uppercase" placeholder="Username" required>
                            </div>
                        </div>
                            <div class="col-md-6">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <input type="password" name="password" class="form-control mb-0 no-uppercase" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <input type="email" name="email" class="form-control mb-0 no-uppercase" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <select name="id_plan" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="card card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <select name="id_role" class="form-select form-control mb-0" aria-label="Database Role" required>
                                        <option value="">Pilih Role Akses</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus"></i> Tambah Akun</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cube"></i> Informasi Akun</h3>
            </div>
            <div class="card-body table-responsive">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                <!-- Search & Per-Page Controls -->
                <form method="GET" action="{{ route('users.profile') }}" class="mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="d-flex align-items-center">
                                <label class="mr-2 mb-0 text-muted" style="font-size:13px;">Tampilkan:</label>
                                <select name="per_page" class="form-control form-control-sm" style="width: auto;" onchange="this.form.submit()">
                                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <span class="ml-2 text-muted" style="font-size:13px;">data</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama, username, email..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('users.profile') }}" class="btn btn-secondary">
                                            <i class="fas fa-undo"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table text-center table-hover table-bordered" style="white-space:nowrap;">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role Database</th>
                                <th>Plan Asal</th>
                                <th>Allowed Plans</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->roleModel)
                                        <span class="badge badge-primary">
                                            <i class="fas fa-user-tag mr-1"></i>{{ $user->roleModel->role_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $rawPlanId = $user->getRawOriginal('id_plan');
                                    @endphp
                                    {{ $rawPlanId ? ($plans->find($rawPlanId)->nama_plan ?? '-') : '-' }}
                                </td>
                                {{-- Kolom Allowed Plans: tampilkan tombol Pop-up jika Manager --}}
                                <td>
                                    @if($user->isManager())
                                        @php $allowedPlans = $user->allowedPlans; @endphp
                                        @if($allowedPlans->count() > 0)
                                            <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#modalPlans{{ $user->id }}" style="border-radius:20px; font-weight:600; font-size:11px;">
                                                <i class="fas fa-building mr-1"></i> {{ $allowedPlans->count() }} Plans
                                            </button>

                                            <!-- Modal Popup Allowed Plans -->
                                            <div class="modal fade" id="modalPlans{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modalPlansLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                                                        <div class="modal-header bg-info text-white py-2">
                                                            <h6 class="modal-title" id="modalPlansLabel{{ $user->id }}" style="font-size:13px; font-weight:700;">
                                                                <i class="fas fa-building mr-1"></i> Allowed Plans - {{ $user->name }}
                                                            </h6>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-left p-3" style="max-height:300px; overflow-y:auto;">
                                                            <ul class="list-group list-group-flush" style="font-size:12px;">
                                                                @foreach($allowedPlans as $ap)
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-1 border-bottom">
                                                                        <span><i class="fas fa-check-circle text-success mr-2"></i>{{ $ap->nama_plan }}</span>
                                                                        @if($ap->abbrivation)
                                                                            <span class="badge badge-light text-muted">({{ $ap->abbrivation }})</span>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        <div class="modal-footer py-1 px-3 bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="font-size:11px;">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary" style="font-size:10px;">Belum ada</span>
                                        @endif
                                    @else
                                        <span class="text-muted" style="font-size:12px;">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('users.edit', $user->uuid) }}" class="btn btn-warning btn-sm" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Tombol Assign Plan: hanya untuk Manager, hanya Superadmin yang bisa --}}
                                    @if($user->isManager() && auth()->user()->id_role == 5)
                                        <a href="{{ route('users.assign-plans', $user->uuid) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Assign Akses Plan ke Manager ini">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('users.destroy', $user->uuid) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Hapus User" onclick="return confirm('Hapus data {{$user->name}}')">
                                            <i class="fas fa-trash"></i> 
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Data pengguna tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Menampilkan Navigasi Pagination Standard Laravel -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection