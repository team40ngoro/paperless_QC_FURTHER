@php
    $rowNumber = 0;
    $showApprovalCheckbox = $showApprovalCheckbox ?? in_array((int) auth()->user()->id_role, [1, 4, 5], true);
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

            $approvalSteps = [
                'QC'  => $item->approved_by_qc,
                'SPV' => $item->approved_by_spv,
            ];
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
            <td class="text-center align-middle">
                <span class="small text-muted">{{ $item->user->name ?? '-' }}</span>
            </td>
            <td class="align-middle">
                <div class="status-badges d-flex flex-column align-items-center">
                    @foreach($approvalSteps as $label => $isApproved)
                        <span @class([
                                'badge',
                                'w-100',
                                'badge-success' => $isApproved,
                                'badge-light border' => !$isApproved,
                                'mb-1' => !$loop->last,
                              ])
                              style="font-size: 0.7rem;">
                            <i class="fas {{ $isApproved ? 'fa-check' : 'fa-clock' }} mr-1"></i> {{ $label }}
                        </span>
                    @endforeach
                    @if($item->approved_by_spv)
                        <small class="text-success font-weight-bold" style="font-size: 0.6rem;">Sudah disetujui oleh SPV QC</small>
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