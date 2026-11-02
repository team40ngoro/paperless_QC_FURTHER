@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-snowflake text-info"></i> Detail Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('chillroom.index') }}">Chillroom</a></li>
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
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-info-circle text-info mr-2"></i> Informasi Detail</h3>
                            <div class="card-tools">
                                <a href="{{ route('chillroom.index') }}" class="btn btn-default btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-calendar-alt mr-2"></i> Data Waktu & Shift</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Tanggal</th>
                                            <td>: {{ $chillroom->tanggal ? \Carbon\Carbon::parse($chillroom->tanggal)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>: <span class="badge badge-secondary">Shift {{ $chillroom->datashift->shift ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jam Kedatangan</th>
                                            <td>: {{ $chillroom->jam_kedatangan ? \Carbon\Carbon::parse($chillroom->jam_kedatangan)->format('H:i') : '-' }} WIB</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-info border-bottom pb-2 mb-3 mt-4"><i class="fas fa-box mr-2"></i> Data Raw Material</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Nama RM</th>
                                            <td>: <strong>{{ $chillroom->nama_rm ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kode Produksi</th>
                                            <td>: <code>{{ $chillroom->kode_produksi ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Berat / Jumlah</th>
                                            <td>: {{ $chillroom->berat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Suhu</th>
                                            <td>
                                                @if($chillroom->suhu == 0 && $chillroom->berat == '-')
                                                    <span class="badge badge-warning"><i class="fas fa-snowflake"></i> FROZEN</span>
                                                @else
                                                    <span class="badge {{ $chillroom->suhu > 5 ? 'badge-danger' : 'badge-success' }}">{{ $chillroom->suhu ?? '0' }}°C</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-check-circle mr-2"></i> Status Pemeriksaan</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Sensori</th>
                                            <td>: 
                                                @if(in_array($chillroom->sensori, ['✔', '✓', 'OK', 'ok']))
                                                    <span class="badge badge-success">OK</span>
                                                @elseif(in_array($chillroom->sensori, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']))
                                                    <span class="badge badge-danger">Tidak OK</span>
                                                @else
                                                    <span>{{ $chillroom->sensori ?? '-' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kemasan</th>
                                            <td>: 
                                                @if(in_array($chillroom->kemasan, ['✔', '✓', 'OK', 'ok']))
                                                    <span class="badge badge-success">OK</span>
                                                @elseif(in_array($chillroom->kemasan, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']))
                                                    <span class="badge badge-danger">Tidak OK</span>
                                                @else
                                                    <span>{{ $chillroom->kemasan ?? '-' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status RM</th>
                                            <td>: <span class="badge {{ $chillroom->status_rm == 'OK' ? 'badge-success' : 'badge-warning' }}">{{ $chillroom->status_rm ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Catatan RM</th>
                                            <td>: {{ $chillroom->catatan_rm ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan Lain</th>
                                            <td>: {{ $chillroom->keterangan ?? '-' }}</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-info border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-check mr-2"></i> Verifikasi</h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="p-2 border rounded {{ $chillroom->approved_by_qc ? 'bg-success-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">QC</small>
                                                <i class="fas {{ $chillroom->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $chillroom->qcApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 border rounded {{ $chillroom->approved_by_spv ? 'bg-dark-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">SPV</small>
                                                <i class="fas {{ $chillroom->approved_by_spv ? 'fa-check-circle text-dark' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $chillroom->spvApprover->name ?? 'Belum' }}</div>
                                                @if($chillroom->approved_by_spv)
                                                    <div class="small font-weight-bold text-dark">Sudah disetujui oleh SPV QC</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $nilaiSampel = json_decode($chillroom->nilai_jumlah_rm, true);
                                $entryKey = array_key_first($nilaiSampel ?? []);
                                $samples = $entryKey ? ($nilaiSampel[$entryKey] ?? []) : [];
                            @endphp

                            @if(!empty($samples))
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-weight mr-2"></i> Detail Sampel Berat</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($samples as $index => $val)
                                                <div class="sample-box p-2 border rounded text-center mr-2 mb-2" style="min-width: 80px; background: #f8f9fa;">
                                                    <small class="text-muted d-block">Sampel {{ $index + 1 }}</small>
                                                    <strong>{{ $val }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    Input oleh: <strong>{{ $chillroom->user->name ?? '-' }}</strong> pada {{ $chillroom->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="btn-group">
                                    @if(auth()->user()->hasPermissionTo('edit-chillroom'))
                                        <a href="{{ route('chillroom.edit', $chillroom->uuid) }}" class="btn btn-warning shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit Data
                                        </a>
                                    @endif
                                    <a href="{{ route('chillroom.logs', $chillroom->uuid) }}" class="btn btn-info ml-2 shadow-sm">
                                        <i class="fas fa-history mr-1"></i> Lihat Log
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $latestQcVerification = $chillroom->qcVerifications->first();
                $isQcUser = auth()->check() && auth()->user()->isQc();
            @endphp

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-clipboard-check text-success mr-2"></i> Verifikasi QC</h3>
                            <div class="card-tools">
                                <a href="{{ route('chillroom.qc-verifications.index', $chillroom->uuid) }}" class="btn btn-success btn-sm shadow-sm">
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
                                <div class="alert {{ $latestQcVerification->status === 'OK' ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm">
                                    <h5 class="mb-1">
                                        <i class="fas {{ $latestQcVerification->status === 'OK' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        Status verifikasi saat ini: <strong>{{ $latestQcVerification->status_label }}</strong>
                                    </h5>
                                    <div class="small">
                                        Diverifikasi oleh <strong>{{ $latestQcVerification->verifiedBy->name ?? '-' }}</strong>
                                        pada {{ $latestQcVerification->verified_at ? $latestQcVerification->verified_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    @if($latestQcVerification->suhu_rm !== null)
                                        <div class="small mt-1"><strong>Suhu RM:</strong> {{ $latestQcVerification->suhu_rm }} °C</div>
                                    @endif
                                    @if($latestQcVerification->sensori !== null)
                                        <div class="small mt-1">
                                            <strong>Sensori:</strong>
                                            @if(in_array($latestQcVerification->sensori, ['✔', '✓', 'OK', 'ok']))
                                                <span class="badge badge-success">Oke</span>
                                            @else
                                                <span class="badge badge-danger">Tidak Oke</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($latestQcVerification->note)
                                        <div class="small mt-1"><strong>Catatan:</strong> {{ $latestQcVerification->note }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-light border shadow-sm">
                                    <i class="fas fa-info-circle mr-1"></i> Data ini belum diverifikasi oleh QC.
                                </div>
                            @endif

                            @if($isQcUser)
                                <form action="{{ route('chillroom.qc-verifications.store', $chillroom->uuid) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="font-weight-bold">Validasi Data <span class="text-danger">*</span></label>
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div class="custom-control custom-checkbox mr-4 mb-2">
                                                <input type="checkbox" class="custom-control-input qc-verification-checkbox" id="qcStatusOk" data-value="OK"
                                                    {{ old('status', $latestQcVerification->status ?? '') === 'OK' ? 'checked' : '' }}>
                                                <label class="custom-control-label text-success font-weight-bold" for="qcStatusOk">
                                                    <i class="fas fa-check-circle mr-1"></i> Data Oke
                                                </label>
                                            </div>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input qc-verification-checkbox" id="qcStatusNotOk" data-value="NOT_OK"
                                                    {{ old('status', $latestQcVerification->status ?? '') === 'NOT_OK' ? 'checked' : '' }}>
                                                <label class="custom-control-label text-danger font-weight-bold" for="qcStatusNotOk">
                                                    <i class="fas fa-times-circle mr-1"></i> Data Tidak Oke
                                                </label>
                                            </div>
                                        </div>
                                        <input type="hidden" name="status" id="qcVerificationStatus" value="{{ old('status', $latestQcVerification->status ?? '') }}">
                                        @error('status')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="qcSuhuRm" class="font-weight-bold">Input Suhu RM (°C)</label>
                                        <input type="number" step="0.1" min="-60" max="60" name="suhu_rm" id="qcSuhuRm"
                                            class="form-control @error('suhu_rm') is-invalid @enderror"
                                            placeholder="Masukkan suhu RM"
                                            value="{{ old('suhu_rm', $latestQcVerification->suhu_rm ?? '') }}">
                                        @error('suhu_rm')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="qcSensori" class="font-weight-bold">Sensori</label>
                                        <select name="sensori" id="qcSensori" class="form-control @error('sensori') is-invalid @enderror">
                                            <option value="">Pilih Status Sensori</option>
                                            <option value="✔" {{ in_array(old('sensori', $latestQcVerification->sensori ?? ''), ['✔', '✓', 'OK', 'ok']) ? 'selected' : '' }}>✓ Oke</option>
                                            <option value="✘" {{ in_array(old('sensori', $latestQcVerification->sensori ?? ''), ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']) ? 'selected' : '' }}>✘ Tidak Oke</option>
                                        </select>
                                        @error('sensori')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="qcVerificationNote" class="font-weight-bold">Catatan Verifikasi <small class="text-muted">(opsional)</small></label>
                                        <textarea name="note" id="qcVerificationNote" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Tambahkan catatan verifikasi jika diperlukan">{{ old('note') }}</textarea>
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
        </div>
    </section>
</div>

<style>
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.2) !important; }
    .bg-primary-light { background-color: rgba(0, 123, 255, 0.1); border-color: rgba(0, 123, 255, 0.2) !important; }
    .bg-dark-light { background-color: rgba(52, 58, 64, 0.1); border-color: rgba(52, 58, 64, 0.2) !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.qc-verification-checkbox');
        const statusInput = document.getElementById('qcVerificationStatus');

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    checkboxes.forEach(function (other) {
                        if (other !== checkbox) {
                            other.checked = false;
                        }
                    });
                    statusInput.value = this.dataset.value;
                } else {
                    statusInput.value = '';
                }
            });
        });
    });
</script>
@endpush
@endsection
