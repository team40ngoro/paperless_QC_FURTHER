@php
use Illuminate\Support\Facades\Schema;
@endphp

@if((isset($areaProses) && count($areaProses)) || (isset($records) && count($records)))
<div class="table-responsive">
    <table class="table table-bordered table-hover text-center" style="white-space: nowrap;">
        <thead class="">
            <tr>
                <th style="width: 50px;">No</th>
                <th>Rincian Pemeriksaan</th>
                <th>Dibuat Oleh</th>
                <th>Status Appr</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = isset($areaProses) ? $areaProses->firstItem() : (isset($records) ? $records->firstItem() : 1); 
                $dataGroup = $groupedData ?? $records;
            @endphp
            @forelse($dataGroup as $groupKey => $groupItems)
                @php
                    $firstItem = $groupItems->first();
                    $tanggalGroup = \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d');
                    $shiftIdGroup = $firstItem->shift_id;
                    $areaIdGroup = $firstItem->area_id;
                    $shiftName = $firstItem->shift->shift ?? 'Unknown';
                    $areaName = $firstItem->area->area ?? 'Unknown';
                    
                    // Check if all items in group are approved
                    $allApprovedQC = $groupItems->every(fn($i) => $i->approved_by_qc);
                    $allApprovedProduksi = $groupItems->every(fn($i) => $i->approved_by_produksi);
                    $allApprovedSPV = $groupItems->every(fn($i) => $i->approved_by_spv);

                    $userRole = auth()->user()->id_role ?? null;
                    // Can approve logic
                    $canApproveQC = in_array($userRole, [1, 3, 5]) && !$allApprovedQC;
                    $canApproveProduksi = in_array($userRole, [1, 2, 5]) && $allApprovedQC && !$allApprovedProduksi;
                    $canApproveSPV = in_array($userRole, [1, 4, 5]) && $allApprovedProduksi && !$allApprovedSPV;
                @endphp

                {{-- Group Header Row --}}
                <tr class="bg-light">
                    <td colspan="5" class="text-left py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge badge-info mr-2">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ \Carbon\Carbon::parse($tanggalGroup)->format('d-M-Y') }}
                                </span>
                                <span class="badge badge-primary mr-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    Shift {{ $shiftName }}
                                </span>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $areaName }}
                                </span>
                                <span class="ml-3 text-muted">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    {{ $groupItems->count() }} Pemeriksaan
                                </span>
                            </div>
                            <div class="btn-group">
                                <span class="mr-2 text-muted font-weight-bold small text-uppercase">VERIFIKASI GROUP:</span>
                                @if($canApproveQC)
                                    <button type="button" class="btn btn-sm btn-success bulk-approve-btn" 
                                        data-tanggal="{{ $tanggalGroup }}" data-shift="{{ $shiftIdGroup }}" data-area="{{ $areaIdGroup }}" data-type="qc">
                                        <i class="fas fa-check-circle mr-1"></i> Approve QC
                                    </button>
                                @elseif($allApprovedQC)
                                    <span class="badge badge-success mr-1"><i class="fas fa-check"></i> QC OK</span>
                                @endif

                                @if($canApproveProduksi)
                                    <button type="button" class="btn btn-sm btn-primary bulk-approve-btn" 
                                        data-tanggal="{{ $tanggalGroup }}" data-shift="{{ $shiftIdGroup }}" data-area="{{ $areaIdGroup }}" data-type="produksi">
                                        <i class="fas fa-check-circle mr-1"></i> Approve Produksi
                                    </button>
                                @elseif($allApprovedProduksi)
                                    <span class="badge badge-primary mr-1"><i class="fas fa-check"></i> Produksi OK</span>
                                @endif

                                @if($canApproveSPV)
                                    <button type="button" class="btn btn-sm btn-dark bulk-approve-btn" 
                                        data-tanggal="{{ $tanggalGroup }}" data-shift="{{ $shiftIdGroup }}" data-area="{{ $areaIdGroup }}" data-type="spv">
                                        <i class="fas fa-check-circle mr-1"></i> Approve SPV
                                    </button>
                                @elseif($allApprovedSPV)
                                    <span class="badge badge-dark mr-1"><i class="fas fa-check"></i> SPV OK</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>

                @foreach($groupItems->sortByDesc('created_at') as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td class="text-left">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-light border mr-2">
                                        <i class="fas fa-clock mr-1 text-primary"></i>
                                        {{ isset($item->jam) && $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                    </span>
                                    <small class="mr-2">
                                        Kerbersihan Ruangan: <span class="badge {{ $item->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $item->kebersihan_ruangan }}</span>
                                    </small>
                                    <small class="mr-2">
                                        Kebersihan Karyawan: <span class="badge {{ $item->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $item->kebersihan_karyawan }}</span>
                                    </small>
                                    <small>
                                        Suhu: <b>{{ $item->pemeriksaan_suhu_ruang }}°C</b>
                                    </small>
                                    @if($item->ketidaksesuaian)
                                        <span class="badge badge-warning ml-2" title="{{ $item->ketidaksesuaian }}">!</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">
                                <i class="fas fa-user"></i>
                                {{ $item->user->name ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column align-items-center" style="gap: 2px;">
                                <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.65rem;">
                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                                </span>
                                <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.65rem;">
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                                </span>
                                <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.65rem;">
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                                </span>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info mb-2" type="button" data-toggle="collapse" data-target="#collapseRiwayat{{ $groupKey }}" aria-expanded="false" aria-controls="collapseRiwayat{{ $groupKey }}">
                                <i class="fas fa-eye"></i> Rincian Jam Pengecekan
                            </button>
                            <div class="btn-group d-block">
                                <x-area-proses-buttons :item="$item" :is-audit-page="$isAuditPage ?? false" />
                            </div>
                            @if(!isset($isAuditPage) || !$isAuditPage)
                            <div class="mt-2 d-flex justify-content-center">
                                <x-audit-dropdown :item="$item" route-prefix="area-proses" />
                            </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                {{-- Row for Collapse --}}
                <tr>
                    <td colspan="5" class="p-0 border-0">
                        <div class="collapse" id="collapseRiwayat{{ $groupKey }}">
                            <div class="card card-body shadow-none border-bottom p-3 bg-light">
                                <h6 class="font-weight-bold text-secondary mb-2 text-left">
                                    <i class="fas fa-clock mr-1"></i> Rincian Pengecekan (Per 2 Jam)
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover text-center shadow-xs bg-white mb-0">
                                        <thead class="bg-gradient-light border-top">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>Jam</th>
                                                <!-- <th>Kebersihan Ruangan</th> -->
                                                <!-- <th>Kebersihan Karyawan</th> -->
                                                <th>Suhu Ruang (°C)</th>
                                                <!-- <th>Suhu RM</th>
                                                <th>Keterangan & Koreksi</th>
                                                <th>Pencatat</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $historyRecords = isset($firstItem->groupRecords) && $firstItem->groupRecords->count() > 0
                                                    ? $firstItem->groupRecords->where('is_audit', $firstItem->is_audit)->sortBy('jam')->values()
                                                    : $groupItems->sortBy('jam')->values();
                                            @endphp
                                            @foreach($historyRecords as $index => $record)
                                                <tr>
                                                    <td class="align-middle font-weight-bold text-secondary">{{ $index + 1 }}</td>
                                                    <td class="align-middle">
                                                        <span class="badge badge-light border px-2 py-1 shadow-sm font-weight-bold">
                                                            <i class="fas fa-clock mr-1 text-primary"></i>
                                                            {{ $record->jam ? \Carbon\Carbon::parse($record->jam)->format('H:i') : '-' }}
                                                        </span>
                                                    </td>
                                                    <!-- <td class="align-middle">
                                                        <span class="badge shadow-sm {{ $record->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }} px-3 py-2" style="min-width: 80px;">
                                                            <i class="fas {{ $record->kebersihan_ruangan === 'OK' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                            {{ $record->kebersihan_ruangan }}
                                                        </span>
                                                    </td> -->
                                                    <!-- <td class="align-middle">
                                                        <span class="badge shadow-sm {{ $record->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }} px-3 py-2" style="min-width: 80px;">
                                                            <i class="fas {{ $record->kebersihan_karyawan === 'OK' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                            {{ $record->kebersihan_karyawan }}
                                                        </span>
                                                    </td> -->
                                                    <td class="align-middle font-weight-bold h6 text-primary mb-0">{{ $record->pemeriksaan_suhu_ruang }} °C</td>
                                                    <!-- <td class="align-middle font-weight-bold h6 text-info mb-0">{{ $record->suhu_rm ?? '-' }}</td> -->
                                                    <!-- <td class="text-left align-middle" style="min-width: 250px;">
                                                        @if($record->ketidaksesuaian || $record->tindakan_koreksi)
                                                            <div class="mb-1">
                                                                <small class="text-danger font-weight-bold">Ketidaksesuaian:</small>
                                                                <div class="p-1 px-2 border rounded bg-light small">{{ $record->ketidaksesuaian ?: '-' }}</div>
                                                            </div>
                                                            <div>
                                                                <small class="text-success font-weight-bold">Tindakan Koreksi:</small>
                                                                <div class="p-1 px-2 border rounded bg-light small">{{ $record->tindakan_koreksi ?: '-' }}</div>
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted font-italic small py-2">
                                                                <i class="fas fa-minus mr-1"></i> Tidak ada ketidaksesuaian
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle text-muted">
                                                        <div class="font-weight-bold text-dark">{{ $record->user->name ?? '-' }}</div>
                                                        <small style="font-size: 0.65rem;">{{ $record->created_at ? $record->created_at->format('d/m/Y H:i') : '' }}</small>
                                                    </td> -->
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-2"></i>
    <h5>Belum ada data Area Proses</h5>
    <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    /**
     * Handle Bulk Approval Button Click
     */
    $('.bulk-approve-btn').on('click', function () {
        var $btn = $(this);
        var tanggal = $btn.data('tanggal');
        var shiftId = $btn.data('shift');
        var areaId = $btn.data('area');
        var type = $btn.data('type');
        
        var typeLabels = { qc: 'QC', produksi: 'Produksi', spv: 'SPV' };

        Swal.fire({
            title: 'Konfirmasi Approval',
            text: 'Apakah Anda yakin ingin menyetujui semua data pemeriksaan pada tanggal ' + tanggal + ' untuk ' + (typeLabels[type] || type) + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '{{ route("area-proses.bulk-approve") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        tanggal: tanggal,
                        shift_id: shiftId,
                        area_id: areaId,
                        type: type
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                            $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Approve ' + typeLabels[type]);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Approve ' + typeLabels[type]);
                    }
                });
            }
        });
    });
});

function showKondisiBarang(kondisi) {
    let icon = '';
    if (kondisi.toLowerCase() === 'baik') {
        icon = '✓ OK';
    } else if (kondisi.toLowerCase() === 'rusak') {
        icon = '✗ Tidak OK';
    } else {
        icon = kondisi;
    }

    Swal.fire({
        title: 'Kondisi Barang',
        html: `<strong>${icon}</strong>`,
        icon: 'info',
        confirmButtonText: 'Tutup'
    });
}
</script>
@endpush