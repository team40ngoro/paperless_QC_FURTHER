@php
    $colspanTotal = (isset($isAuditPage) && $isAuditPage) ? 11 : 12;
    $rowNumber = (isset($records) && method_exists($records, 'firstItem')) ? ($records->firstItem() ?? 1) : 1;
@endphp

@forelse($records as $item)
    @php
        $isFullyApproved = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
    @endphp
    <tr class="item-row {{ $isFullyApproved ? 'row-verified' : '' }}" data-uuid="{{ $item->uuid }}">
        @unless(isset($isAuditPage) && $isAuditPage)
            <td class="text-center align-middle" style="width: 40px;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <input type="checkbox" class="form-check-input row-checkbox" 
                           data-uuid="{{ $item->uuid }}" 
                           title="Pilih baris ini">
                </div>
            </td>
        @endunless
        <td class="text-center align-middle">
            {{ $rowNumber }}
        </td>
        <td class="align-middle text-center">
            @php
                $sVal = (string)($item->shift->shift ?? ($item->shift_id ?? ''));
                $shiftLabel = $sVal;
                if ($sVal === '1' || strcasecmp($sVal, 'shift 1') === 0 || strcasecmp($sVal, 'pagi') === 0) {
                    $shiftLabel = 'Pagi (Shift 1)';
                } elseif ($sVal === '2' || strcasecmp($sVal, 'shift 2') === 0 || strcasecmp($sVal, 'sore') === 0) {
                    $shiftLabel = 'Sore (Shift 2)';
                } elseif ($sVal === '3' || strcasecmp($sVal, 'shift 3') === 0 || strcasecmp($sVal, 'malam') === 0) {
                    $shiftLabel = 'Malam (Shift 3)';
                }
            @endphp
            <span class="badge {{ ($item->shift_id ?? null) == 1 ? 'bg-primary' : (($item->shift_id ?? null) == 2 ? 'bg-success' : 'bg-secondary') }}">
                {{ $shiftLabel }}
            </span>
        </td>
        <td class="align-middle text-center">
            <span class="badge badge-secondary">
                @php
                    $userRole = auth()->user()->id_role ?? null;
                    $showTime = in_array($userRole, [1, 2, 5]);
                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                @endphp
                {{ $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format($format) : $item->tanggal->format($format)) : '-' }}
            </span>
        </td>
        <td class="align-middle text-center">
            <span>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
        </td>
        <td class="align-middle text-center">
            <code>{{ $item->kode_produksi_emulsi ?? '-' }}</code>
        </td>
        <td class="align-middle text-left">
            <div class="font-weight-bold">{{ $item->produk->nama_produk ?? '-' }}</div>
            <small class="text-muted">{{ $item->berat_sampling ?? '-' }} gram</small>
        </td>
        <td class="align-middle text-center">
            <span class="badge badge-outline-primary">{{ $item->nama_emulsi->nama_emulsi ?? '-' }}</span>
        </td>
        <td class="align-middle text-center">
            <span>{{ $item->nomor_emulsi->nomor_emulsi ?? '-' }}</span>
        </td>
        <td class="align-middle text-center">
            <span class="font-weight-bold">{{ $item->user->name ?? '-' }}</span>
            <br><small class="badge badge-light border">1. Produksi</small>
        </td>
        <td class="align-middle">
            <div class="status-badges d-flex flex-column align-items-center">
                <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;" title="2. Verifikasi QC Inspektor">
                    <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> 2. QC Inspektor
                </span>
                <span class="badge {{ $item->approved_by_produksi ? 'badge-primary' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;" title="3. Diketahui SPV Produksi">
                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> 3. SPV Produksi
                </span>
                <span class="badge {{ $item->approved_by_spv ? 'badge-warning' : 'badge-light border' }} w-100" style="font-size: 0.7rem;" title="4. Disetujui SPV QC">
                    <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> 4. SPV QC
                </span>
            </div>
        </td>
        <td class="text-center align-middle">
            <x-action-buttons :item="$item" route-prefix="persiapan-bahan-emulsi" :show-print="true" />
            @if(!isset($isAuditPage) || !$isAuditPage)
            <x-audit-dropdown :item="$item" route-prefix="persiapan-bahan-emulsi" :uuid="$item->uuid" />
            @endif
        </td>
    </tr>
    @php $rowNumber++; @endphp
@empty
    <tr>
        <td colspan="{{ $colspanTotal }}" class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data ditemukan</p>
            </div>
        </td>
    </tr>
@endforelse
