@extends('layouts.app')

@section('container')
@php
    $latestQcVerification = $seasoning->latestQcVerification;
    $qcVerificationNotOk = $latestQcVerification && $latestQcVerification->overall_status !== 'OK';
    $isQcUser = auth()->check() && (auth()->user()->isQcInspector() || auth()->user()->isSuperAdmin());
    $isSpvUser = auth()->check() && ((int) auth()->user()->id_role === 4 || auth()->user()->isSuperAdmin());
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-pepper-hot text-primary"></i> Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('seasoning.index') }}">Seasoning</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-info-circle text-primary mr-2"></i> Informasi Detail</h3>
                            <div class="card-tools">
                                <a href="{{ route('seasoning.index') }}" class="btn btn-default btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-calendar-alt mr-2"></i> Data Waktu & Shift</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Tanggal</th>
                                            <td>: {{ $seasoning->tanggal ? \Carbon\Carbon::parse($seasoning->tanggal)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>: <span class="badge badge-primary">Shift {{ $seasoning->shift->shift ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>: {{ $seasoning->jam ? \Carbon\Carbon::parse($seasoning->jam)->format('H:i') : '-' }} WIB</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-mortar-pestle mr-2"></i> Data RM / Seasoning</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Nama RM</th>
                                            <td>: <strong>{{ $seasoning->nama_rm ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kode Produksi</th>
                                            <td>: <code>{{ $seasoning->kode_produksi ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Berat</th>
                                            <td>: {{ $seasoning->berat ?? '-' }} kg</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-check-circle mr-2"></i> Status Pemeriksaan</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Sensori (QC)</th>
                                            <td>:
                                                @if($latestQcVerification)
                                                    @if($latestQcVerification->sensori_status === 'OK')
                                                        <span class="badge badge-success">Oke</span>
                                                    @elseif($latestQcVerification->sensori_status === 'NOT_OK')
                                                        <span class="badge badge-danger">Tidak Oke</span>
                                                    @else
                                                        <span class="badge badge-light border">Belum diverifikasi QC</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light border">Belum diverifikasi QC</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kemasan (Input)</th>
                                            <td>: {{ $seasoning->kemasan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kemasan (QC)</th>
                                            <td>:
                                                @if($latestQcVerification)
                                                    @if($latestQcVerification->kemasan_status === 'OK')
                                                        <span class="badge badge-success">Oke</span>
                                                    @elseif($latestQcVerification->kemasan_status === 'NOT_OK')
                                                        <span class="badge badge-danger">Tidak Oke</span>
                                                    @else
                                                        <span class="badge badge-light border">Belum diverifikasi QC</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light border">Belum diverifikasi QC</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kondisi Fisik (QC)</th>
                                            <td>:
                                                @if($latestQcVerification)
                                                    @if($latestQcVerification->kondisi_fisik_status === 'OK')
                                                        <span class="badge badge-success">Oke</span>
                                                    @elseif($latestQcVerification->kondisi_fisik_status === 'NOT_OK')
                                                        <span class="badge badge-danger">Tidak Oke</span>
                                                    @else
                                                        <span class="badge badge-light border">Belum diverifikasi QC</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light border">Belum diverifikasi QC</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>: {{ $seasoning->keterangan ?? '-' }}</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-check mr-2"></i> Verifikasi</h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="p-2 border rounded {{ $qcVerificationNotOk ? 'bg-danger-light' : ($seasoning->approved_by_qc ? 'bg-success-light' : 'bg-light') }}">
                                                <small class="d-block text-muted">QC</small>
                                                <i class="fas {{ $qcVerificationNotOk ? 'fa-times-circle text-danger' : ($seasoning->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted') }} fa-lg my-2"></i>
                                                <div class="small">{{ $seasoning->qcApprover->name ?? 'Belum' }}</div>
                                                @if($qcVerificationNotOk)
                                                    <div class="small font-weight-bold text-danger">Ada item tidak oke</div>
                                                @elseif($seasoning->approved_by_qc)
                                                    <div class="small font-weight-bold text-success">Sudah diverifikasi QC</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 border rounded {{ $seasoning->approved_by_spv ? 'bg-dark-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">SPV QC</small>
                                                <i class="fas {{ $seasoning->approved_by_spv ? 'fa-check-circle text-dark' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $seasoning->spvApprover->name ?? 'Belum' }}</div>
                                                @if($seasoning->approved_by_spv)
                                                    <div class="small font-weight-bold text-dark">Sudah disetujui oleh SPV QC</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    Input oleh: <strong>{{ $seasoning->createdBy->name ?? '-' }}</strong> pada {{ $seasoning->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="btn-group">
                                    @if(auth()->user()->hasPermissionTo('edit-seasoning'))
                                        <a href="{{ route('seasoning.edit', $seasoning->uuid) }}" class="btn btn-warning shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit Data
                                        </a>
                                    @endif
                                    <a href="{{ route('seasoning.logs', $seasoning->uuid) }}" class="btn btn-info ml-2 shadow-sm">
                                        <i class="fas fa-history mr-1"></i> Lihat Log
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-clipboard-check text-success mr-2"></i> Verifikasi QC</h3>
                            <div class="card-tools">
                                <a href="{{ route('seasoning.qc-verifications.index', $seasoning->uuid) }}" class="btn btn-success btn-sm shadow-sm">
                                    <i class="fas fa-list mr-1"></i> Log Verifikasi QC
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            @if($latestQcVerification)
                                <div class="alert {{ $latestQcVerification->overall_status === 'OK' ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm">
                                    <h5 class="mb-1">
                                        <i class="fas {{ $latestQcVerification->overall_status === 'OK' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        Status verifikasi saat ini: <strong>{{ $latestQcVerification->overall_status_label }}</strong>
                                    </h5>
                                    <div class="small">
                                        Diverifikasi oleh <strong>{{ $latestQcVerification->verifiedBy->name ?? '-' }}</strong>
                                        pada {{ $latestQcVerification->verified_at ? $latestQcVerification->verified_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    <div class="small mt-1">
                                        <strong>Waktu Pengecekan, Raw Material, Kode Produksi, Berat:</strong>
                                        <span class="badge {{ $latestQcVerification->data_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $latestQcVerification->data_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                        </span>
                                    </div>
                                    <div class="small mt-1">
                                        <strong>Sensori:</strong>
                                        <span class="badge {{ $latestQcVerification->sensori_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $latestQcVerification->sensori_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                        </span>
                                    </div>
                                    <div class="small mt-1">
                                        <strong>Kemasan:</strong>
                                        <span class="badge {{ $latestQcVerification->kemasan_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $latestQcVerification->kemasan_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                        </span>
                                    </div>
                                    <div class="small mt-1">
                                        <strong>Kondisi Fisik:</strong>
                                        @if($latestQcVerification->kondisi_fisik_status === 'OK')
                                            <span class="badge badge-success">Oke</span>
                                        @elseif($latestQcVerification->kondisi_fisik_status === 'NOT_OK')
                                            <span class="badge badge-danger">Tidak Oke</span>
                                        @else
                                            <span class="badge badge-light border">-</span>
                                        @endif
                                    </div>
                                    @if($latestQcVerification->note)
                                        <div class="small mt-1"><strong>Keterangan:</strong> {{ $latestQcVerification->note }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-light border shadow-sm">
                                    <i class="fas fa-info-circle mr-1"></i> Data ini belum diverifikasi oleh QC.
                                </div>
                            @endif

                            @if($isQcUser)
                                <form action="{{ route('seasoning.qc-verifications.store', $seasoning->uuid) }}" method="POST">
                                    @csrf
                                    @php
                                        $groups = [
                                            'data_status' => 'Waktu Pengecekan, Raw Material, Kode Produksi, Berat',
                                            'sensori_status' => 'Sensori',
                                            'kemasan_status' => 'Kemasan',
                                            'kondisi_fisik_status' => 'Kondisi Fisik',
                                        ];
                                    @endphp
                                    @foreach($groups as $field => $label)
                                        <div class="form-group">
                                            <label class="font-weight-bold">{{ $label }} <span class="text-danger">*</span></label>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <div class="custom-control custom-checkbox mr-4 mb-2">
                                                    <input type="checkbox" class="custom-control-input qc-verification-checkbox" id="{{ $field }}Ok" data-target="{{ $field }}" data-value="OK"
                                                        {{ old($field, $latestQcVerification->{$field} ?? '') === 'OK' ? 'checked' : '' }}>
                                                    <label class="custom-control-label text-success font-weight-bold" for="{{ $field }}Ok">
                                                        <i class="fas fa-check-circle mr-1"></i> Oke
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox" class="custom-control-input qc-verification-checkbox" id="{{ $field }}NotOk" data-target="{{ $field }}" data-value="NOT_OK"
                                                        {{ old($field, $latestQcVerification->{$field} ?? '') === 'NOT_OK' ? 'checked' : '' }}>
                                                    <label class="custom-control-label text-danger font-weight-bold" for="{{ $field }}NotOk">
                                                        <i class="fas fa-times-circle mr-1"></i> Tidak Oke
                                                    </label>
                                                </div>
                                            </div>
                                            @if($field === 'kondisi_fisik_status')
                                                <div class="small text-muted mt-1">
                                                    <div><strong>v = Oke:</strong> sesuai dengan spesifikasi, tidak mengalami perubahan secara fisik, tidak menggumpal, tidak basah.</div>
                                                    <div><strong>x = Tidak Oke:</strong> tidak sesuai spesifikasi, kondisi basah, menggumpal.</div>
                                                </div>
                                            @endif
                                            <input type="hidden" name="{{ $field }}" id="{{ $field }}Value" value="{{ old($field, $latestQcVerification->{$field} ?? '') }}">
                                            @error($field)
                                                <small class="text-danger d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endforeach
                                    <div class="form-group">
                                        <label for="qcVerificationNote" class="font-weight-bold">Keterangan</label>
                                        <textarea name="note" id="qcVerificationNote" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Tambahkan keterangan verifikasi">{{ old('note', $latestQcVerification->note ?? '') }}</textarea>
                                        @error('note')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success shadow-sm">
                                        <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-light border mb-0">
                                    <i class="fas fa-lock mr-1"></i> Hanya role QC yang dapat mengubah verifikasi.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-dark card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-user-shield text-dark mr-2"></i> Approval SPV QC</h3>
                        </div>
                        <div class="card-body">
                            @if($seasoning->approved_by_spv)
                                <div class="alert alert-success border-0 shadow-sm mb-0">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Data sudah disetujui oleh <strong>{{ $seasoning->spvApprover->name ?? '-' }}</strong>
                                    pada {{ $seasoning->spv_approved_at ? $seasoning->spv_approved_at->format('d/m/Y H:i') : '-' }}.
                                </div>
                            @elseif(!$seasoning->approved_by_qc)
                                <div class="alert alert-warning border-0 shadow-sm mb-0">
                                    <i class="fas fa-clock mr-1"></i> Menunggu verifikasi QC sebelum dapat disetujui SPV QC.
                                </div>
                            @elseif($isSpvUser)
                                <p class="text-muted">QC sudah melakukan verifikasi. Silakan setujui data ini sebagai SPV QC.</p>
                                <button type="button" id="btnApproveSpv" class="btn btn-dark shadow-sm">
                                    <i class="fas fa-check mr-1"></i> Approve SPV QC
                                </button>
                            @else
                                <div class="alert alert-light border mb-0">
                                    <i class="fas fa-lock mr-1"></i> Menunggu approval SPV QC.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.2) !important; }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.2) !important; }
    .bg-primary-light { background-color: rgba(0, 123, 255, 0.1); border-color: rgba(0, 123, 255, 0.2) !important; }
    .bg-dark-light { background-color: rgba(52, 58, 64, 0.1); border-color: rgba(52, 58, 64, 0.2) !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.qc-verification-checkbox');

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const target = this.dataset.target;
                const group = document.querySelectorAll('.qc-verification-checkbox[data-target="' + target + '"]');
                const hiddenInput = document.getElementById(target + 'Value');

                if (this.checked) {
                    group.forEach(function (other) {
                        if (other !== checkbox) {
                            other.checked = false;
                        }
                    });
                    hiddenInput.value = this.dataset.value;
                } else {
                    hiddenInput.value = '';
                }
            });
        });

        const approveButton = document.getElementById('btnApproveSpv');
        if (approveButton) {
            approveButton.addEventListener('click', function () {
                if (!confirm('Setujui data ini sebagai SPV QC?')) {
                    return;
                }

                approveButton.disabled = true;

                fetch("{{ route('seasoning.approve', $seasoning->uuid) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: 'spv' })
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (result.ok && result.data.success) {
                            window.location.reload();
                            return;
                        }

                        alert(result.data.message || 'Gagal menyetujui data.');
                        approveButton.disabled = false;
                    })
                    .catch(function () {
                        alert('Gagal menyetujui data.');
                        approveButton.disabled = false;
                    });
            });
        }
    });
</script>
@endpush
@endsection
