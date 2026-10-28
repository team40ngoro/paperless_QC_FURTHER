@php
    $colspanTotal = (isset($isAuditPage) && $isAuditPage) ? 13 : 14;
    $rowNumber = (isset($records) && method_exists($records, 'firstItem')) ? ($records->firstItem() ?? 1) : 1;
@endphp

@forelse($records as $groupKey => $groupItems)
    @foreach($groupItems as $item)
        @php
            $prefixedUuid = ($item->jenis === 'Forming' ? 'forming::' : 'non-forming::') . $item->uuid;
            $isFullyApproved = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
        @endphp
        <tr class="item-row {{ $isFullyApproved ? 'row-verified' : '' }}" data-uuid="{{ $item->uuid }}">
            @unless(isset($isAuditPage) && $isAuditPage)
                <td class="text-center align-middle" style="width: 40px;">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <input type="checkbox" class="form-check-input row-checkbox" 
                               data-uuid="{{ $prefixedUuid }}" 
                               title="Pilih baris ini">
                    </div>
                </td>
            @endunless
            <td class="text-center align-middle">
                {{ $rowNumber }}
            </td>
            <td class="align-middle">
                <span class="badge {{ $item->jenis === 'Forming' ? 'bg-primary' : 'bg-secondary' }}">{{ $item->jenis }}</span>
            </td>
            <td class="align-middle">
                @if(($item->shift_id ?? null) == 1)
                    <span class="badge bg-primary">Shift {{ $item->shift ?? 'Shift 1' }}</span>
                @elseif(($item->shift_id ?? null) == 2)
                    <span class="badge bg-success">Shift {{ $item->shift ?? 'Shift 2' }}</span>
                @else
                    <span class="badge bg-secondary">Shift {{ $item->shift ?? ($item->shift_id ?? '-') }}</span>
                @endif
            </td>
            <td class="align-middle">
                <span class="badge badge-secondary">
                    @php
                        $userRole = auth()->user()->id_role ?? null;
                        $showTime = in_array($userRole, [1, 2, 5]);
                        $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                    @endphp
                    {{ $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format($format) : $item->tanggal->format($format)) : '-' }}
                </span>
            </td>
            <td class="align-middle">
                <span>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
            </td>
            <td class="align-middle text-left">
                <div class="font-weight-bold">{{ $item->nama_produk ?? '-' }}</div>
                <small class="text-muted">{{ $item->berat_sampling ?? '-' }} gram</small>
            </td>
            <td class="align-middle">
                <code>{{ $item->kode_produksi ?? '-' }}</code>
            </td>
            <td class="align-middle">
                <span>{{ $item->nomor_formula ?? '-' }}</span>
            </td>
            <td class="text-center align-middle">
                @if(($item->kondisi ?? '') === '✔')
                    <span class="" style="font-size: 1.1rem;">✔</span>
                @elseif(($item->kondisi ?? '') === '✘')
                    <span class="" style="font-size: 1.1rem;">✘</span>
                @else
                    <span>{{ $item->kondisi ?? '-' }}</span>
                @endif
            </td>
            <td class="align-middle text-center">
                <span>{{ $item->dibuat_oleh ?? '-' }}</span>
            </td>
            <td class="align-middle">
                <div class="status-badges d-flex flex-column align-items-center">
                    <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                    </span>
                    <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                    </span>
                    <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                    </span>
                </div>
            </td>
            <td class="align-middle">
                <small class="text-muted d-block text-truncate" style="max-width: 150px;" title="{{ $item->catatan }}">
                    {{ $item->catatan ?? '-' }}
                </small>
            </td>
            <td class="text-center align-middle">
                @if($item->jenis === 'Forming')
                    <x-action-buttons :item="$item" route-prefix="persiapan-bahan-forming" :show-history="true" :show-view="true" :show-print="true" />
                    @if(!isset($isAuditPage) || !$isAuditPage)
                    <x-audit-dropdown :item="$item" route-prefix="persiapan-bahan-forming" :uuid="$item->uuid" :has-audit-version="isset($item->has_audit_version) ? $item->has_audit_version : false" />
                    @endif
                @else
                    <x-action-buttons :item="$item" route-prefix="persiapan-bahan-non-forming" :show-history="false" :show-view="true" :show-print="true" />
                    @if(!isset($isAuditPage) || !$isAuditPage)
                    <x-audit-dropdown :item="$item" route-prefix="persiapan-bahan-non-forming" :uuid="$item->uuid" :has-audit-version="isset($item->has_audit_version) ? $item->has_audit_version : false" />
                    @endif
                @endif
            </td>
        </tr>
        @php $rowNumber++; @endphp
    @endforeach
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
