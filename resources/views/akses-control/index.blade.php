@extends('layouts.app')

@push('css')
<style>
    .role-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .role-btn {
        flex: 1 1 auto;
        text-align: center;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 2px solid transparent;
        background-color: #f8f9fa;
        color: #495057;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .role-btn:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        color: #212529;
    }

    .role-btn.active {
        background-color: #007bff;
        color: #fff;
        border-color: #0056b3;
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }

    .table-permissions th {
        background-color: #f4f6f9;
        color: #333;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        vertical-align: middle !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    .table-permissions td {
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .table-permissions tbody tr:hover {
        background-color: #f1f5f9;
    }

    .module-name {
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    .module-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: #6c757d;
    }

    /* Custom Checkbox Styling */
    .custom-control-input:checked ~ .custom-control-label::before {
        border-color: #28a745;
        background-color: #28a745;
    }
    
    .custom-control-label {
        cursor: pointer;
    }

    .card-header-custom {
        border-bottom: 1px solid rgba(0,0,0,.125);
        background-color: #fff;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    .save-btn-wrapper {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 0 0 0.25rem 0.25rem;
        border-top: 1px solid #e9ecef;
    }
</style>
@endpush

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-user-shield text-primary mr-2"></i> Access Control
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Access Control</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Kelola hak akses (View, Create, Edit, Delete) untuk setiap Role di Sistem QC.</p>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <!-- Role Selection Selector -->
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3 text-secondary">
                                <i class="fas fa-users mr-2"></i> Pilih Role
                            </h5>
                            <div class="d-flex flex-wrap">
                                @foreach($roles as $role)
                                    <a href="{{ route('access-control.index', ['role_id' => $role->id]) }}" 
                                       class="btn {{ ($selectedRole && $selectedRole->id == $role->id) ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }} m-1 px-4 py-2 font-weight-bold">
                                        {{ ucfirst($role->role) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Matrix -->
                @if($selectedRole)
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-header card-header-custom py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold m-0 text-dark">
                                Konfigurasi Akses: <span class="text-primary">{{ ucfirst($selectedRole->role) }}</span>
                            </h3>
                        </div>
                        
                        <div class="card-body p-0">
                            <form action="{{ route('access-control.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">
                                
                                <div class="table-responsive">
                                    <table class="table table-permissions mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%; padding-left: 25px; vertical-align: middle;">Modul / Form QC</th>
                                                <th class="text-center" style="width: 16%;">
                                                    <i class="fas fa-eye text-info mr-1 mb-2"></i> View <br>
                                                    <div class="custom-control custom-switch custom-switch-sm d-inline-block">
                                                        <input type="checkbox" class="custom-control-input check-all-col" data-col="view" id="checkAllView">
                                                        <label class="custom-control-label text-muted" for="checkAllView" style="font-size: 0.75rem; font-weight: normal;">Semua</label>
                                                    </div>
                                                </th>
                                                <th class="text-center" style="width: 16%;">
                                                    <i class="fas fa-plus text-success mr-1 mb-2"></i> Create <br>
                                                    <div class="custom-control custom-switch custom-switch-sm d-inline-block">
                                                        <input type="checkbox" class="custom-control-input check-all-col" data-col="create" id="checkAllCreate">
                                                        <label class="custom-control-label text-muted" for="checkAllCreate" style="font-size: 0.75rem; font-weight: normal;">Semua</label>
                                                    </div>
                                                </th>
                                                <th class="text-center" style="width: 16%;">
                                                    <i class="fas fa-edit text-warning mr-1 mb-2"></i> Edit <br>
                                                    <div class="custom-control custom-switch custom-switch-sm d-inline-block">
                                                        <input type="checkbox" class="custom-control-input check-all-col" data-col="edit" id="checkAllEdit">
                                                        <label class="custom-control-label text-muted" for="checkAllEdit" style="font-size: 0.75rem; font-weight: normal;">Semua</label>
                                                    </div>
                                                </th>
                                                <th class="text-center" style="width: 16%;">
                                                    <i class="fas fa-trash text-danger mr-1 mb-2"></i> Delete <br>
                                                    <div class="custom-control custom-switch custom-switch-sm d-inline-block">
                                                        <input type="checkbox" class="custom-control-input check-all-col" data-col="delete" id="checkAllDelete">
                                                        <label class="custom-control-label text-muted" for="checkAllDelete" style="font-size: 0.75rem; font-weight: normal;">Semua</label>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($modules as $module)
                                            <tr>
                                                <td style="padding-left: 20px;">
                                                    <div class="module-name">
                                                        <div class="module-icon">
                                                            <i class="fas fa-folder-open"></i>
                                                        </div>
                                                        {{ $module['label'] }}
                                                    </div>
                                                </td>
                                                <!-- Action Columns -->
                                                @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                                    @php $permValue = $module['permissions'][$action]; @endphp
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input permission-checkbox col-{{ $action }}" 
                                                                   data-col="{{ $action }}"
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permValue }}" 
                                                                   id="perm_{{ $action }}_{{ $module['id'] }}"
                                                                   {{ in_array($permValue, $rolePermissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="perm_{{ $action }}_{{ $module['id'] }}"></label>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="save-btn-wrapper p-2 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-md shadow-sm font-weight-bold px-5">
                                        <i class="fas fa-save mr-2"></i> Simpan Konfigurasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shield-alt text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                            <h4 class="text-secondary font-weight-bold">Belum Ada Role yang Dipilih</h4>
                            <p class="text-muted">Silakan pilih salah satu role pada menu di atas untuk mengatur hak akses secara spesifik.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAllSwitches = document.querySelectorAll('.check-all-col');
        
        if (checkAllSwitches.length > 0) {
            // Update the status of column switches based on checked checkboxes
            const updateCheckAllStatus = () => {
                ['view', 'create', 'edit', 'delete'].forEach(action => {
                    const checkboxes = document.querySelectorAll('.col-' + action);
                    const checkedBoxes = document.querySelectorAll('.col-' + action + ':checked');
                    const switchEl = document.getElementById('checkAll' + action.charAt(0).toUpperCase() + action.slice(1));
                    
                    if (switchEl && checkboxes.length > 0) {
                        switchEl.checked = (checkedBoxes.length === checkboxes.length);
                    }
                });
            };
            
            // Initialization
            updateCheckAllStatus();

            // Handle "Pilih Semua per Kolom" Toggle
            checkAllSwitches.forEach(switchEl => {
                switchEl.addEventListener('change', function() {
                    const col = this.dataset.col;
                    const checkboxes = document.querySelectorAll('.col-' + col);
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                });
            });

            // Handle individual checkbox changes
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');
            allCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateCheckAllStatus);
            });
        }
    });
</script>
@endpush
