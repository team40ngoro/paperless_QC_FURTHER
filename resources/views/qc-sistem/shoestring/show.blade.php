@extends('layouts.app')

@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Data Shoestring</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Data Utama</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 35%">Shift</th>
                                            <td>{{ $shoestring->shift->shift ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Produksi</th>
                                            <td>
                                                @php
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $showTime = in_array($userRole, [1, 2, 5]);
                                                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                @endphp
                                                {{ $shoestring->tanggal ? \Carbon\Carbon::parse($shoestring->tanggal)->format($format) : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>{{ $shoestring->jam ? \Carbon\Carbon::parse($shoestring->jam)->format('H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Produsen</th>
                                            <td>{{ $shoestring->nama_produsen ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 35%">Kode Produksi</th>
                                            <td>{{ $shoestring->kode_produksi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Expired</th>
                                            <td>{{ $shoestring->best_before ? \Carbon\Carbon::parse($shoestring->best_before)->format('d-m-Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gramase (gram)</th>
                                            <td>{{ $shoestring->berat_sampling ? $shoestring->berat_sampling . ' gram' : '-' }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <th>Pemeriksaan Defect</th>
                                            <td>{{ $shoestring->sampling_defect ?? '-' }}</td>
                                        </tr> -->
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $shoestring->catatan ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-12">
                                <h5 class="mb-3 font-weight-bold text-dark"><i class="fas fa-exclamation-circle text-warning mr-1"></i> Rincian Temuan Defect</h5>
                                     @if(!empty($defectRows))
                                         <div class="table-responsive">
                                             <table class="table table-bordered table-hover text-center" style="min-width: 600px;">
                                                 <thead class="bg-light text-uppercase small font-weight-bold">
                                                     <tr>
                                                         <th class="text-left py-3" style="width: 25%;">Kategori Ketidaksesuaian</th>
                                                         <th class="text-left py-3" style="width: 25%;">Jenis Ketidaksesuaian</th>
                                                         <th class="text-left py-3" style="width: 20%;">Sub Jenis</th>
                                                         <th class="text-left py-3" style="width: 18%;">Spesifikasi</th>
                                                         <th class="py-3" style="width: 12%;">Jumlah (Qty)</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     @foreach($defectRows as $row)
                                                         <tr>
                                                             <td class="text-left align-middle font-weight-bold text-secondary">
                                                                 <i class="fas fa-folder mr-1 text-info"></i> {{ $row['kategori'] }}
                                                             </td>
                                                             <td class="text-left align-middle text-dark">
                                                                 {{ $row['jenis'] }}
                                                             </td>
                                                             <td class="text-left align-middle">
                                                                 @if(!empty($row['sub_jenis']) && $row['sub_jenis'] !== '-')
                                                                     <span class="badge badge-warning px-2 py-1" style="font-size: 0.8rem; font-weight: 500; white-space: normal; text-align: left;">{{ $row['sub_jenis'] }}</span>
                                                                 @else
                                                                     <span class="text-muted font-italic">-</span>
                                                                 @endif
                                                             </td>
                                                             <td class="text-left align-middle">
                                                                 @if(!empty($row['spesifikasi']) && $row['spesifikasi'] !== '-')
                                                                     <span class="badge badge-success px-2 py-1" style="font-size: 0.8rem; font-weight: 500;">{{ $row['spesifikasi'] }}</span>
                                                                 @else
                                                                     <span class="text-muted font-italic">-</span>
                                                                 @endif
                                                             </td>
                                                             <td class="align-middle">
                                                                 <strong class="text-primary" style="font-size: 1rem;">{{ $row['qty'] }}</strong>
                                                             </td>
                                                         </tr>
                                                         @endforeach
                                                         <tr>
                                                             <th>Total Defect</th>
                                                             <td colspan="4">{{ $latestQcVerification ? ($latestQcVerification->total_defect ?? '-') : ($shoestring->total_defect ?? '-') }}</td>
                                                         </tr>
                                                 </tbody>
                                             </table>
                                         </div>
                                     @else
                                         <div class="alert alert-light border text-center py-4">
                                             <i class="fas fa-info-circle text-muted mr-1"></i> Tidak ada rincian defect ditemukan.
                                         </div>
                                     @endif

                                    @if($shoestring->dokumentasi && is_array($shoestring->dokumentasi) && count($shoestring->dokumentasi) > 0)
                                        <h5 class="mt-4">Dokumentasi Label Supplier</h5>
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                                            @foreach($shoestring->dokumentasi as $doc)
                                                <a href="{{ asset($assetPath . 'storage/' . $doc) }}" target="_blank">
                                                    <img src="{{ asset($assetPath . 'storage/' . $doc) }}" class="img-thumbnail" style="height: 150px; width: 150px; object-fit: cover;">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <a href="{{ route('shoestring.index') }}" class="btn btn-secondary btn-md ml-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    @php
                        $currentRole = (int) (auth()->user()->id_role ?? 0);
                        $roleName = strtolower(trim(auth()->user()->roleModel->role ?? auth()->user()->role ?? ''));
                        $canVerifyQc = in_array($currentRole, [1, 3, 5], true);
                        $canApproveProduksi = in_array($currentRole, [1, 5, 8], true) || in_array($roleName, ['produksi spv', 'spv produksi', 'foreman produksi'], true);
                        $canApproveSpv = in_array($currentRole, [1, 4, 5], true);
                    @endphp

                    <div class="card card-info mt-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clipboard-check mr-1"></i> Verifikasi QC</h3>
                        </div>
                        <div class="card-body">
                            @if($latestQcVerification)
                                <div class="alert {{ $latestQcVerification->verifikasi === 'OK' ? 'alert-success' : 'alert-danger' }}">
                                    <strong>Hasil Verifikasi:</strong> {{ $latestQcVerification->verifikasiLabel }}
                                    <br>
                                    <small>
                                        Diverifikasi oleh {{ $latestQcVerification->verifiedBy->name ?? '-' }}
                                        @if($latestQcVerification->verifiedBy)
                                            <span class="badge badge-light border ml-1">{{ $latestQcVerification->verifiedBy->roleModel->role ?? $latestQcVerification->verifiedBy->role ?? '-' }}</span>
                                        @endif
                                        @if($latestQcVerification->verified_at)
                                            pada {{ \Carbon\Carbon::parse($latestQcVerification->verified_at)->format('d-m-Y H:i') }}
                                        @endif
                                    </small>
                                    @if($latestQcVerification->note)
                                        <br><strong>Catatan:</strong> {{ $latestQcVerification->note }}
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle mr-1"></i> Belum ada verifikasi QC untuk data ini.
                                </div>
                            @endif

                            @if($canVerifyQc)
                                <form action="{{ route('shoestring.qc-verifications.store', $shoestring->uuid) }}" method="POST" id="qc-verification-form">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Hasil Verifikasi <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="verifikasi" class="form-control" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="OK" {{ old('verifikasi', $latestQcVerification->verifikasi ?? '') === 'OK' ? 'selected' : '' }}>Oke</option>
                                                <option value="NOT_OK" {{ old('verifikasi', $latestQcVerification->verifikasi ?? '') === 'NOT_OK' ? 'selected' : '' }}>Tidak Oke</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Defect &amp; Jumlah</label>
                                        <div class="col-sm-9">
                                            <div class="card card-outline card-secondary mb-2">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="card-title text-primary m-0" style="font-size:0.95rem;font-weight:600;">
                                                        <i class="fas fa-exclamation-triangle"></i> Daftar Item Defect
                                                    </h6>
                                                </div>
                                                <div class="card-body py-2 px-3" id="qc-defect-container">
                                                    <div class="text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data defect...</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <strong>Total Defect: <span id="qc-total-defect" class="text-danger">{{ $latestQcVerification ? ($latestQcVerification->total_defect ?? 0) : ($shoestring->total_defect ?? 0) }}</span></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Catatan</label>
                                        <div class="col-sm-9">
                                            <textarea name="note" class="form-control" rows="2" placeholder="Catatan verifikasi (opsional)">{{ old('note', $latestQcVerification->note ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-save mr-1"></i> Simpan Verifikasi
                                        </button>
                                    </div>
                                </form>
                            @endif

                            @if($qcVerifications->isNotEmpty())
                                <h6 class="mt-4 mb-2 font-weight-bold">Riwayat Verifikasi</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Waktu</th>
                                                <th>Hasil</th>
                                                <th>Total Defect</th>
                                                <th>Catatan</th>
                                                <th>Oleh</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($qcVerifications as $verification)
                                                <tr>
                                                    <td>{{ $verification->verified_at ? \Carbon\Carbon::parse($verification->verified_at)->format('d-m-Y H:i') : '-' }}</td>
                                                    <td>
                                                        <span class="badge {{ $verification->verifikasi === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $verification->verifikasiLabel }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $verification->total_defect ?? '-' }}</td>
                                                    <td>{{ $verification->note ?? '-' }}</td>
                                                    <td>
                                                        {{ $verification->verifiedBy->name ?? '-' }}
                                                        @if($verification->verifiedBy)
                                                            <br><small class="text-muted">{{ $verification->verifiedBy->roleModel->role ?? $verification->verifiedBy->role ?? '-' }}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card card-success mt-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-signature mr-1"></i> Pengesahan</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th style="width: 35%;">Verifikasi</th>
                                            <td>:
                                                @if($shoestring->approved_by_qc)
                                                    <strong>{{ $shoestring->qcApprover->name ?? '-' }}</strong>
                                                    <span class="text-muted">({{ $shoestring->qcApprover->roleModel->role ?? $shoestring->qcApprover->role ?? '-' }})</span>
                                                @else
                                                    <span class="text-muted font-italic">Belum diverifikasi</span>
                                                    @if($canVerifyQc)
                                                        <button type="button" class="btn btn-sm btn-info btn-approve ml-2" data-type="qc">
                                                            <i class="fas fa-check mr-1"></i> Verifikasi
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Disetujui</th>
                                            <td>:
                                                @if($shoestring->approved_by_spv)
                                                    <strong>{{ $shoestring->spvApprover->name ?? '-' }}</strong>
                                                    <span class="text-muted">({{ $shoestring->spvApprover->roleModel->role ?? $shoestring->spvApprover->role ?? '-' }})</span>
                                                @else
                                                    <span class="text-muted font-italic">Belum disetujui</span>
                                                    @if($canApproveSpv)
                                                        <button type="button" class="btn btn-sm btn-success btn-approve ml-2" data-type="spv">
                                                            <i class="fas fa-check mr-1"></i> Setujui
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th style="width: 35%;">Dibuat oleh</th>
                                            <td>:
                                                <strong>{{ $shoestring->createdBy->name ?? '-' }}</strong>
                                                @if($shoestring->createdBy)
                                                    <span class="text-muted">({{ $shoestring->createdBy->roleModel->role ?? $shoestring->createdBy->role ?? '-' }})</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Diketahui oleh</th>
                                            <td>:
                                                @if($shoestring->approved_by_produksi)
                                                    <strong>{{ $shoestring->produksiApprover->name ?? '-' }}</strong>
                                                    <span class="text-muted">({{ $shoestring->produksiApprover->roleModel->role ?? $shoestring->produksiApprover->role ?? '-' }})</span>
                                                @else
                                                    <span class="text-muted font-italic">Belum diketahui</span>
                                                    @if($canApproveProduksi)
                                                        <button type="button" class="btn btn-sm btn-primary btn-approve ml-2" data-type="produksi">
                                                            <i class="fas fa-check mr-1"></i> Setujui
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('scripts')
<script>
$(function () {
    const produsen = @json($shoestring->nama_produsen);
    const existingQty = @json($qtyMap);
    const $container = $('#qc-defect-container');

    function updateTotal() {
        let total = 0;
        $container.find('.qc-defect-qty').each(function () {
            const v = $(this).val();
            if (v !== '' && !isNaN(v)) {
                total += parseFloat(v);
            }
        });
        $('#qc-total-defect').text(total);
    }

    function renderDefects(categories) {
        if (!categories || !categories.length) {
            $container.html('<div class="text-muted font-italic">Tidak ada data defect untuk produsen ini.</div>');
            updateTotal();
            return;
        }

        let html = '';
        categories.forEach(function (cat) {
            html += '<div class="qc-defect-category mb-3">';
            html += '<h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2"><i class="fas fa-folder-open"></i> ' + (cat.kategori || '-') + '</h6>';

            if (cat.items && cat.items.length) {
                html += '<div class="row no-gutters mb-1 px-1">'
                    + '<div class="col-4 small font-weight-bold text-muted">Jenis</div>'
                    + '<div class="col-3 small font-weight-bold text-muted">Sub Jenis</div>'
                    + '<div class="col-3 small font-weight-bold text-muted">Spesifikasi</div>'
                    + '<div class="col-2 small font-weight-bold text-muted text-center">Qty</div>'
                    + '</div>';

                cat.items.forEach(function (item) {
                    const label = item.spesifikasi ? (item.jenis + ' - ' + item.spesifikasi) : item.jenis;
                    const val = (existingQty && existingQty[label] !== undefined) ? existingQty[label] : '';

                    html += '<div class="row no-gutters align-items-center mb-2 px-1">'
                        + '<div class="col-4 small">' + item.jenis + '</div>'
                        + '<div class="col-3 small text-muted">' + (item.sub_jenis || '-') + '</div>'
                        + '<div class="col-3 small text-muted">' + (item.spesifikasi || '-') + '</div>'
                        + '<div class="col-2"><input type="text" class="form-control form-control-sm qc-defect-qty text-center" name="sampling_defect_qty[' + label + ']" value="' + val + '" placeholder="0"></div>'
                        + '</div>';
                });
            } else {
                html += '<div class="text-muted font-italic small">Tidak ada item defect.</div>';
            }

            html += '</div>';
        });

        $container.html(html);
        updateTotal();
    }

    if ($container.length) {
        $.ajax({
            url: '{{ route("ajax.get-defect-by-produsen") }}',
            type: 'GET',
            data: { nama_produsen: produsen },
            success: function (response) {
                if (response.success && response.detail_defect) {
                    renderDefects(response.detail_defect);
                } else {
                    $container.html('<div class="text-warning"><i class="fas fa-exclamation-circle"></i> Tidak ada data defect untuk produsen ini.</div>');
                }
            },
            error: function () {
                $container.html('<div class="text-danger"><i class="fas fa-times-circle"></i> Gagal memuat data defect.</div>');
            }
        });
    }

    $(document).on('input', '.qc-defect-qty', updateTotal);

    $(document).on('click', '.btn-approve', function () {
        const $btn = $(this);
        const type = $btn.data('type');
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("shoestring.approve", $shoestring->uuid) }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                type: type
            },
            success: function () {
                window.location.reload();
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                alert((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan persetujuan.');
            }
        });
    });
});
</script>
@endpush
