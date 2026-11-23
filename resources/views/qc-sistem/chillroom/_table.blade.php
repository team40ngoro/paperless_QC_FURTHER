@php
    $rowNumber = 0;
    $showApprovalCheckbox = $showApprovalCheckbox ?? (in_array((int) auth()->user()->id_role, [1, 4, 5], true) || auth()->user()->canApproveAsProduksi());
@endphp
@forelse($records as $groupItems)
    @foreach($groupItems as $item)
        @php
            $rowNumber++; // Increment nomor baris global
            $isVerified = $item->approved_by_qc && $item->approved_by_spv;

            $tanggalFormatted = $item->tanggal
                ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')
                : '-';

            $jamFormatted = $item->jam_kedatangan
                ? \Carbon\Carbon::parse($item->jam_kedatangan)->format('H:i')
                : '-';

            $suhuIsDanger = $item->suhu > 5;

            $qcVerificationNotOk = $item->latestQcVerification
                && $item->latestQcVerification->status === 'NOT_OK';
        @endphp
        <tr @class(['item-row', 'row-verified' => $isVerified]) data-uuid="{{ $item->uuid }}">
            @if($showApprovalCheckbox)
                <td class="text-center align-middle" style="width: 40px;">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <input type="checkbox" class="form-check-input row-checkbox bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
                    </div>
                </td>
            @endif
            <td class="text-center align-middle">{{ $rowNumber }}</td>
            <td class="text-center align-middle">
                <span class="badge badge-outline-info">Shift {{ $item->datashift->shift ?? '-' }}</span>
            </td>
            <td class="text-center align-middle">{{ $tanggalFormatted }}</td>
            <td class="text-center align-middle">{{ $jamFormatted }}</td>
            <td class="align-middle">
                <div class="font-weight-bold">{{ $item->nama_rm ?? '-' }}</div>
            </td>
            <td class="text-center align-middle"><code>{{ $item->kode_produksi ?? '-' }}</code></td>
            <td class="text-center align-middle">{{ $item->berat ?? '-' }}</td>
            <td class="text-center align-middle font-weight-bold">
                {{ $item->suhu ?? '0' }}°C
            </td>
            <td class="align-middle">
                <div class="small">
                    <i class="fas fa-user-edit text-muted mr-1"></i>{{ $item->user->name ?? '-' }}
                    @if($item->user)
                        <span class="text-muted">({{ $item->user->roleModel->role ?? $item->user->role ?? '-' }})</span>
                    @endif
                </div>
                <div class="small">
                    @if($item->approved_by_produksi)
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $item->produksiApprover->name ?? 'SPV Produksi' }}</span>
                        @if($item->produksiApprover)
                            <span class="text-muted">({{ $item->produksiApprover->roleModel->role ?? $item->produksiApprover->role ?? '-' }})</span>
                        @endif
                    @else
                        <span class="text-muted"><i class="far fa-clock mr-1"></i>Belum diketahui</span>
                    @endif
                </div>
            </td>
            <td class="align-middle">
                <div class="small">
                    @if($qcVerificationNotOk)
                        <span class="text-danger"><i class="fas fa-times-circle mr-1"></i>{{ $item->qcApprover->name ?? 'Tidak Oke' }}</span>
                        <span class="badge badge-danger ml-1">Tidak Oke</span>
                    @elseif($item->approved_by_qc)
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $item->qcApprover->name ?? 'QC Inspector' }}</span>
                        @if($item->qcApprover)
                            <span class="text-muted">({{ $item->qcApprover->roleModel->role ?? $item->qcApprover->role ?? '-' }})</span>
                        @endif
                    @else
                        <span class="text-muted"><i class="far fa-clock mr-1"></i>Belum verifikasi</span>
                    @endif
                </div>
                <div class="small">
                    @if($item->approved_by_spv)
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $item->spvApprover->name ?? 'SPV QC' }}</span>
                        @if($item->spvApprover)
                            <span class="text-muted">({{ $item->spvApprover->roleModel->role ?? $item->spvApprover->role ?? '-' }})</span>
                        @endif
                    @else
                        <span class="text-muted"><i class="far fa-clock mr-1"></i>Belum disetujui</span>
                    @endif
                </div>
            </td>
            <td class="text-center align-middle">
                <x-action-buttons
                    :item="$item"
                    route-prefix="chillroom"
                    permission-prefix="chillroom"
                    :show-history="true"
                    :show-view="true"
                    :show-print="false"
                    :show-qc-verification-log="true"
                    layout="horizontal"
                    class="tombol"
                />
                @unless(isset($isAuditPage) && $isAuditPage)
                    <x-audit-dropdown :item="$item" route-prefix="chillroom"/>
                @endunless
            </td>
        </tr>
    @endforeach
@empty
    <tr>
        <td colspan="{{ $showApprovalCheckbox ? 12 : 11 }}" class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Tidak ada data Chillroom ditemukan.</p>
        </td>
    </tr>
@endforelse
<style>
    /* Memastikan tombol aksi berbentuk kotak dan tidak gepeng */
    .tombol {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
    }
    .tombol i {
        font-size: 0.85rem !important;
    }
</style>